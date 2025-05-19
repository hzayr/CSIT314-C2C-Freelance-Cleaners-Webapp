<?php
session_start();
require_once "../connectDatabase.php";

// Entity Layer
class CleaningServiceEntity {
    private static $conn;
    
    public static function connect() {
        if (!self::$conn) {
            $database = new Database();
            self::$conn = $database->getConnection();
        }
    }

    public static function getAllServices($buyer_id) {
        self::connect();
        $stmt = self::$conn->prepare("
            SELECT 
                cs.service_id, 
                cs.service_title, 
                sc.category as service_category, 
                cs.service_price, 
                cs.service_description, 
                p.user_id, 
                p.first_name, 
                p.last_name,
                CASE WHEN s.service_id IS NOT NULL THEN 1 ELSE 0 END as is_shortlisted
            FROM 
                cleaningservices cs 
            JOIN 
                profile p ON cs.cleaner_id = p.user_id
            JOIN
                service_categories sc ON cs.service_category = sc.category_id
            LEFT JOIN
                shortlist s ON cs.service_id = s.service_id AND s.user_id = ?
            WHERE
                sc.status_id = 1
        ");
        $stmt->bind_param("i", $buyer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
    }

    public static function searchServices($buyer_id, $criteria, $search) {
        self::connect();
        $query = "
            SELECT 
                cs.service_id, 
                cs.service_title, 
                sc.category as service_category, 
                cs.service_price, 
                cs.service_description, 
                p.user_id, 
                p.first_name, 
                p.last_name,
                CASE WHEN s.service_id IS NOT NULL THEN 1 ELSE 0 END as is_shortlisted
            FROM 
                cleaningservices cs 
            JOIN 
                profile p ON cs.cleaner_id = p.user_id
            JOIN
                service_categories sc ON cs.service_category = sc.category_id
            LEFT JOIN
                shortlist s ON cs.service_id = s.service_id AND s.user_id = ?
            WHERE
                sc.status_id = 1
        ";

        if ($criteria && $search) {
            if ($criteria === 'category') {
                $query .= " AND sc.category LIKE ? ORDER BY sc.category ASC";
            } else {
                $query .= " AND cs.$criteria LIKE ? ORDER BY cs.$criteria ASC";
            }
            $stmt = self::$conn->prepare($query);
            $search = "%$search%";
            $stmt->bind_param("is", $buyer_id, $search);
        } else if ($criteria) {
            if ($criteria === 'category') {
                $query .= " ORDER BY sc.category ASC";
            } else {
                $query .= " ORDER BY cs.$criteria ASC";
            }
            $stmt = self::$conn->prepare($query);
            $stmt->bind_param("i", $buyer_id);
        } else {
            $stmt = self::$conn->prepare($query);
            $stmt->bind_param("i", $buyer_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
    }
}

// Control Layer
class SearchCleaningServiceController {
    private $entity;

    public function __construct() {
        $this->entity = new CleaningServiceEntity();
    }

    public function getAllServices($buyer_id) {
        return CleaningServiceEntity::getAllServices($buyer_id);
    }

    public function searchCleaningService($buyer_id, $criteria, $search) {
        return CleaningServiceEntity::searchServices($buyer_id, $criteria, $search);
    }
}

// Boundary Layer
class SearchCleaningServicePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function getBuyerID() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function SearchCleaningServiceUI() {
        $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $searchCleaningService = isset($_POST['searchButton']);
        $buyerID = $this->getBuyerID();

        if ($searchCleaningService) {
            $services = $this->controller->searchCleaningService($buyerID, $criteria, $search);
        } else {
            $services = $this->controller->getAllServices($buyerID);
        }
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Homeowner Dashboard</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }

                .header-container {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem;
                    background-color: #343a40;
                    color: #ffffff;
                }

                .logo {
                    font-size: 1.5rem;
                    font-weight: bold;
                    color: #ffffff;
                    text-decoration: none;
                }

                .nav-buttons {
                    display: flex;
                    gap: 1rem;
                }

                .nav-button {
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.9rem;
                    transition: background-color 0.3s;
                }

                .nav-button:hover {
                    background-color: #0056b3;
                }

                .logout-button {
                    background-color: #dc3545;
                    color: white;
                    text-decoration: none;
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    font-size: 0.9rem;
                    transition: background-color 0.3s;
                }

                .logout-button:hover {
                    background-color: #c82333;
                }

                .action-buttons {
                    display: flex;
                    gap: 0.5rem;
                    justify-content: center;
                }

                .action-button {
                    min-width: 150px;
                    text-align: center;
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
                    background-color: white;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                table,
                th,
                td {
                    border: 1px solid #dee2e6;
                }

                th,
                td {
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

                .shortlist-button {
                    background-color: #007bff;
                    color: white;
                    text-align: center;
                    text-decoration: none;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                    margin: 2px;
                }

                .shortlist-button:hover {
                    background-color: #0056b3;
                }

                .listing-button {
                    background-color: #28a745;
                    color: white;
                    text-align: center;
                    text-decoration: none;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                    margin: 2px;
                }

                .listing-button:hover {
                    background-color: #218838;
                }

                .search-button {
                    background-color: #007bff;
                    color: white;
                    text-align: center;
                    text-decoration: none;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                }

                .search-button:hover {
                    background-color: #0056b3;
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

                /* Add popup styles */
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

                .popup-message {
                    margin-bottom: 20px;
                    font-size: 1.1em;
                }

                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }

                .popup-confirm, .popup-cancel {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 1em;
                }

                .popup-confirm {
                    background-color: #28a745;
                    color: white;
                }

                .popup-cancel {
                    background-color: #dc3545;
                    color: white;
                }

                .popup-confirm:hover {
                    background-color: #218838;
                }

                .popup-cancel:hover {
                    background-color: #c82333;
                }

                /* Add notification styles */
                .notification {
                    display: none;
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 25px;
                    border-radius: 4px;
                    color: white;
                    z-index: 1000;
                }

                .notification.success {
                    background-color: #28a745;
                }

                .notification.error {
                    background-color: #dc3545;
                }
            </style>
        </head>
        <body>
            <!-- Add popup HTML -->
            <div id="shortlistPopup" class="popup-overlay">
                <div class="popup-content">
                    <div class="popup-message">Are you sure you want to add this service to your shortlist?</div>
                    <div class="popup-buttons">
                        <button id="confirmShortlist" class="popup-confirm">Add to Shortlist</button>
                        <button id="cancelShortlist" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Add notification div -->
            <div id="notification" class="notification"></div>

            <div class="header-container">
                <a href="../index.html" class="logo">clean.sg</a>
                <div class="nav-buttons">
                    <a href="homeowner_manage_profile.php?user_id=<?php echo $buyerID; ?>" class="nav-button">Manage Account</a>
                    <a href="homeowner_view_shortlist.php?user_id=<?php echo $buyerID; ?>" class="nav-button">View Shortlist</a>
                    <a href="homeowner_view_matches.php?user_id=<?php echo $buyerID; ?>" class="nav-button">View Matches</a>
                    <a href="homeowner_view_history.php?user_id=<?php echo $buyerID; ?>" class="nav-button">View History</a>
                </div>
                <a href="../logout.php" class="logout-button">Logout</a>
            </div>
            <h2>Available Cleaning Services</h2>
            
            <form method="POST" action="homeowner_dashboard.php" class="search-form">
                <label for="service">Search based on:</label>
                <select id="service" name="criteria">
                    <option value="service_title">Service Title</option>
                    <option value="category">Category</option>
                    <option value="service_price">Price</option>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter Text Here" />
                <button class="search-button" type="submit" name="searchButton">Search</button>
            </form>

            <table>
                <tr>
                    <th>Service Title</th>
                    <th>Service Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Cleaner</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['service_title']); ?></td>
                        <td><?php echo htmlspecialchars($service['service_category']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($service['service_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($service['service_description']); ?></td>
                        <td><?php echo htmlspecialchars($service['first_name'] . " " . $service['last_name']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <form action="homeowner_service_details.php" method="post" style="display: inline;">
                                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                    <input type="hidden" name="referrer" value="dashboard">
                                    <button class="listing-button action-button" type="submit">View Service Details</button>
                                </form>

                                <?php if (!$service['is_shortlisted']): ?>
                                    <button class="shortlist-button action-button" onclick="showShortlistPopup(<?php echo $service['service_id']; ?>)">Add to Shortlist</button>
                                <?php else: ?>
                                    <button class="shortlist-button action-button" disabled style="background-color: #cccccc; cursor: not-allowed;">Already Shortlisted</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <script>
                let currentServiceId = null;

                function showShortlistPopup(serviceId) {
                    currentServiceId = serviceId;
                    const popup = document.getElementById('shortlistPopup');
                    popup.style.display = 'block';
                }

                function showNotification(message, type) {
                    const notification = document.getElementById('notification');
                    notification.textContent = message;
                    notification.className = 'notification ' + type;
                    notification.style.display = 'block';

                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 3000);
                }

                document.getElementById('confirmShortlist').onclick = function() {
                    if (!currentServiceId) return;

                    // Make AJAX request to add to shortlist
                    fetch('homeowner_add_shortlist.php?service_id=' + currentServiceId, {
                        method: 'POST'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Update the button state
                            const button = document.querySelector(`button[onclick="showShortlistPopup(${currentServiceId})"]`);
                            if (button) {
                                button.disabled = true;
                                button.style.backgroundColor = '#cccccc';
                                button.style.cursor = 'not-allowed';
                                button.textContent = 'Already Shortlisted';
                                button.removeAttribute('onclick');
                            }
                        } else {
                            showNotification(data.message || 'Failed to add service to shortlist.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while adding to shortlist. Please try again.', 'error');
                    })
                    .finally(() => {
                        document.getElementById('shortlistPopup').style.display = 'none';
                        currentServiceId = null;
                    });
                };

                document.getElementById('cancelShortlist').onclick = function() {
                    document.getElementById('shortlistPopup').style.display = 'none';
                    currentServiceId = null;
                };

                // Close popup when clicking outside
                document.getElementById('shortlistPopup').onclick = function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                        currentServiceId = null;
                    }
                };
            </script>
        </body>
        </html>
        <?php
    }
}

// Instantiate and display the page
$searchCleaningServiceController = new SearchCleaningServiceController();
$searchCleaningServicePage = new SearchCleaningServicePage($searchCleaningServiceController);
$searchCleaningServicePage->SearchCleaningServiceUI();

if (isset($_POST['view'])) {
    $username = urlencode($this->agent->getUsername());
    header("Location: homeowner_view_listings.php?username=" . $username);
    exit();
}

if (isset($_POST['viewMatches'])) {
    header("Location: homeowner_view_matches.php");
    exit();
}

if (isset($_POST['logout'])) {
    // ... existing code ...
}
?>
