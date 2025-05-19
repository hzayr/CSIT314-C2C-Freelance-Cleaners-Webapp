<?php
session_start();
require_once "../connectDatabase.php";

// ENTITY LAYER
class ShortlistEntity
{
    private $conn;

    public $shortlist_id;
    public $service_id;
    public $user_id;
    public $date_added;
    public $service_title;
    public $service_category;
    public $service_price;
    public $service_description;
    public $first_name;
    public $last_name;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getShortlistsByUser($user_id, $criteria = null, $search = null)
    {
        $query = "
            SELECT s.shortlist_id, s.service_id, cs.cleaner_id AS user_id, s.shortlist_date AS date_added, 
                   cs.service_title, sc.category as service_category, cs.service_price, cs.service_description,
                   p.first_name, p.last_name,
                   CASE WHEN m.match_id IS NOT NULL THEN 1 ELSE 0 END AS is_matched
            FROM shortlist s
            JOIN cleaningservices cs ON s.service_id = cs.service_id
            JOIN profile p ON cs.cleaner_id = p.user_id
            JOIN service_categories sc ON cs.service_category = sc.category_id
            LEFT JOIN matches m ON s.service_id = m.service_id AND m.homeowner_id = ?
            WHERE s.user_id = ?
        ";

        if ($criteria && $search) {
            if ($criteria === 'category') {
                $query .= " AND sc.category LIKE ?";
            } else {
                $query .= " AND cs.$criteria LIKE ?";
            }
        }

        $stmt = $this->conn->prepare($query);
        
        if ($criteria && $search) {
            $search = "%$search%";
            $stmt->bind_param("iis", $user_id, $user_id, $search);
        } else {
            $stmt->bind_param("ii", $user_id, $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $shortlists = [];
        while ($row = $result->fetch_assoc()) {
            $shortlists[] = $row;
        }
        return $shortlists;
    }
}

// CONTROL LAYER
class ShortlistController
{
    private $shortlist;

    public function __construct($shortlist)
    {
        $this->shortlist = $shortlist;
    }

    public function getBuyerID()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getShortlists()
    {
        $buyerID = $this->getBuyerID();
        $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        return $buyerID !== null ? $this->shortlist->getShortlistsByUser($buyerID, $criteria, $search) : [];
    }
}

// BOUNDARY LAYER
class ShortlistPage
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function ViewShortlistUI()
    {
        $shortlists = $this->controller->getShortlists();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Shortlist</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }
                header {
                    padding: 20px;
                    background-color: #343a40;
                    color: #ffffff;
                }
                header h1 {
                    margin: 0;
                    font-size: 1.5em;
                }
                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-top: 20px;
                }
                table {
                    width: 90%;
                    margin: 20px auto;
                    border-collapse: collapse;
                    background-color: #ffffff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                table, th, td {
                    border: 1px solid #dee2e6;
                }
                th, td {
                    padding: 12px;
                    text-align: center;
                    color: #343a40;
                }
                th {
                    background-color: #6c757d;
                    color: #ffffff;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f1f1f1;
                }
                /* Buttons */
                .return-button, .view-matches-button {
                    display: inline-block;
                    margin: 5px;
                    padding: 10px 20px;
                    font-size: 16px;
                    border-radius: 5px;
                    color: white;
                    text-decoration: none;
                }
                .return-button {
                    background-color: #6c757d;
                }
                .return-button:hover {
                    background-color: #5a6268;
                }
                .view-matches-button {
                    background-color: #007bff;
                }
                .view-matches-button:hover {
                    background-color: #0056b3;
                }

                .search-button {
                    background-color: #007bff;
                    padding: 10px 15px;
                    font-size: 14px;
                    border-radius: 5px;
                    color: white;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                }
                .search-button:hover {
                    background-color: #0056b3;
                }
                .remove-button {
                    background-color: red;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    color: white;
                    width: 150px;
                    margin: 5px;
                }
                .remove-button:hover {
                    background-color: darkred;
                }
                .match-button {
                    background-color: #28a745;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    color: white;
                    width: 150px;
                    margin: 5px;
                }
                .match-button:hover {
                    background-color: #218838;
                }
                .match-button:disabled {
                    background-color: #6c757d;
                    cursor: not-allowed;
                }
                .listing-button {
                    background-color: #007bff;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    color: white;
                    width: 150px;
                    margin: 5px;
                }
                .listing-button:hover {
                    background-color: #0056b3;
                }

                /* Popup Styles */
                .popup-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                }
                .popup-content {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: white;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }
                .popup-confirm {
                    background-color: #dc3545;
                    color: white;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .popup-cancel {
                    background-color: #6c757d;
                    color: white;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .search-form {
                    text-align: center;
                    margin: 20px 0;
                }
                .search-form select,
                .search-form input[type="text"] {
                    padding: 8px;
                    margin: 0 5px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div id="deletePopup" class="popup-overlay">
                <div class="popup-content">
                    <div class="popup-message">Are you sure you want to remove this service from your shortlist?</div>
                    <div class="popup-buttons">
                        <button id="confirmDelete" class="popup-confirm">Remove</button>
                        <button id="cancelDelete" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <div id="matchPopup" class="popup-overlay">
                <div class="popup-content">
                    <div class="popup-message">Are you sure you want to match with this cleaner?</div>
                    <div class="popup-buttons">
                        <button id="confirmMatch" class="popup-confirm">Match</button>
                        <button id="cancelMatch" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <header>
                <h1>clean.sg</h1>
            </header>
            <h2>Shortlisted Services</h2>

            <form method="POST" action="" class="search-form">
                <label for="service">Search based on:</label>
                <select id="service" name="criteria">
                    <option value="service_title">Service Title</option>
                    <option value="category">Category</option>
                    <option value="service_price">Price</option>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter Text Here" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" />
                <button class="search-button" type="submit" name="searchButton">Search</button>
            </form>

            <table>
                <tr>
                    <th>Service Title</th>
                    <th>Service Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Cleaner</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($shortlists as $shortlist): ?>
                    <tr>
                        <td><?= htmlspecialchars($shortlist['service_title']); ?></td>
                        <td><?= htmlspecialchars($shortlist['service_category']); ?></td>
                        <td>$<?= htmlspecialchars(number_format($shortlist['service_price'], 2)); ?></td>
                        <td><?= htmlspecialchars($shortlist['service_description']); ?></td>
                        <td><?= htmlspecialchars($shortlist['first_name'] . " " . $shortlist['last_name']); ?></td>
                        <td><?= htmlspecialchars($shortlist['date_added']); ?></td>
                        <td>
                            <form action="homeowner_service_details.php" method="post" style="display: inline;">
                                <input type="hidden" name="service_id" value="<?= $shortlist['service_id']; ?>">
                                <input type="hidden" name="referrer" value="shortlist">
                                <button class="listing-button" type="submit">View Service Details</button>
                            </form>
                            <button class="match-button" onclick="showMatchPopup(<?= $shortlist['service_id']; ?>)" <?= $shortlist['is_matched'] ? 'disabled' : ''; ?>>Match with Cleaner</button>
                            <button class="remove-button" onclick="showDeletePopup(<?= $shortlist['shortlist_id']; ?>)">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div style="text-align: center; margin: 20px;">
                <a href="homeowner_dashboard.php" class="return-button">Return to Main Page</a>
                <a href="homeowner_view_matches.php" class="view-matches-button">View Matches</a>
            </div>

            <script>
                let currentShortlistId = null;
                let currentServiceId = null;

                function showDeletePopup(shortlistId) {
                    currentShortlistId = shortlistId;
                    document.getElementById('deletePopup').style.display = 'block';
                }
                function showMatchPopup(serviceId) {
                    currentServiceId = serviceId;
                    document.getElementById('matchPopup').style.display = 'block';
                }
                document.getElementById('confirmDelete').onclick = function() {
                    if (!currentShortlistId) return;
                    fetch('homeowner_delete_shortlist.php?shortlist_id=' + currentShortlistId, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'confirm_delete=1'
                    }).then(response => {
                        if (!response.ok) throw new Error('Network error');
                        window.location.reload();
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    }).finally(() => {
                        document.getElementById('deletePopup').style.display = 'none';
                        currentShortlistId = null;
                    });
                };
                document.getElementById('cancelDelete').onclick = function() {
                    document.getElementById('deletePopup').style.display = 'none';
                    currentShortlistId = null;
                };
                document.getElementById('confirmMatch').onclick = function() {
                    if (!currentServiceId) return;
                    fetch('homeowner_create_match.php?service_id=' + currentServiceId, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'confirm_match=1'
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            alert('Successfully matched!');
                            window.location.reload();
                        } else {
                            alert(data.message || 'An error occurred.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    }).finally(() => {
                        document.getElementById('matchPopup').style.display = 'none';
                        currentServiceId = null;
                    });
                };
                document.getElementById('cancelMatch').onclick = function() {
                    document.getElementById('matchPopup').style.display = 'none';
                    currentServiceId = null;
                };
            </script>
        </body>
        </html>
        <?php
    }
}

// MAIN
$database = new Database();
$conn = $database->getConnection();

$shortlistEntity = new ShortlistEntity($conn);
$controller = new ShortlistController($shortlistEntity);
$page = new ShortlistPage($controller);
$page->ViewShortlistUI();

$database->closeConnection();
?>
