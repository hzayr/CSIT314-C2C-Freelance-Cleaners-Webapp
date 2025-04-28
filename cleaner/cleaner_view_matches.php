<?php
session_start();
require_once "../connectDatabase.php";

class MatchEntity
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getMatchesByCleaner($cleaner_id, $filter = '', $search = '')
    {
        $query = "
            SELECT m.match_id, m.service_id, m.match_date, m.status,
                   cs.service_title, cs.service_type, cs.service_price,
                   p.first_name, p.last_name, u.email, u.phone_num,
                   m.rating, m.review
            FROM matches m
            JOIN cleaningservices cs ON m.service_id = cs.service_id
            JOIN profile p ON m.homeowner_id = p.user_id
            JOIN users u ON m.homeowner_id = u.user_id
            WHERE cs.cleaner_id = ?
        ";

        if (!empty($search) && !empty($filter)) {
            $query .= " AND $filter LIKE ?";
        }

        $query .= " ORDER BY m.match_date DESC";

        $stmt = $this->conn->prepare($query);
        
        if (!empty($search) && !empty($filter)) {
            $search_param = "%$search%";
            $stmt->bind_param("is", $cleaner_id, $search_param);
        } else {
            $stmt->bind_param("i", $cleaner_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }

    public function updateMatchStatus($match_id, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE matches 
            SET status = ?
            WHERE match_id = ?
        ");
        $stmt->bind_param("si", $status, $match_id);
        return $stmt->execute();
    }
}

class ViewMatchesController
{
    private $matchEntity;

    public function __construct($matchEntity)
    {
        $this->matchEntity = $matchEntity;
    }

    public function getCleanerID()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getMatches()
    {
        $cleanerID = $this->getCleanerID();
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        return $cleanerID !== null ? $this->matchEntity->getMatchesByCleaner($cleanerID, $filter, $search) : [];
    }

    public function handleMatchAction($match_id, $action)
    {
        if ($action === 'accept') {
            return $this->matchEntity->updateMatchStatus($match_id, 'accepted');
        } elseif ($action === 'reject') {
            return $this->matchEntity->updateMatchStatus($match_id, 'rejected');
        }
        return false;
    }
}

class ViewMatchesPage
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function ViewMatchesUI()
    {
        $matches = $this->controller->getMatches();
        $current_filter = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : '';
        $current_search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>My Matches</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
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
                .return-button {
                    display: block;
                    margin: 30px auto;
                    padding: 10px 20px;
                    background-color: grey;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    text-align: center;
                    width: fit-content;
                }
                .return-button:hover {
                    background-color: #5a6268;
                }
                .accept-button, .reject-button {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    margin: 0 5px;
                }
                .accept-button {
                    background-color: #28a745;
                    color: white;
                }
                .accept-button:hover {
                    background-color: #218838;
                }
                .reject-button {
                    background-color: #dc3545;
                    color: white;
                }
                .reject-button:hover {
                    background-color: #c82333;
                }
                .status-pending {
                    color: #ffc107;
                    font-weight: bold;
                }
                .status-accepted {
                    color: #28a745;
                    font-weight: bold;
                }
                .status-rejected {
                    color: #dc3545;
                    font-weight: bold;
                }
                .status-completed {
                    color: #17a2b8;
                    font-weight: bold;
                }
                .search-container {
                    width: 90%;
                    margin: 20px auto;
                    text-align: center;
                }
                .search-form {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                    align-items: center;
                }
                .search-input {
                    padding: 8px;
                    width: 300px;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                }
                .filter-select {
                    padding: 8px;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    background-color: white;
                }
                .search-button {
                    padding: 8px 16px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .search-button:hover {
                    background-color: #0056b3;
                }
                .clear-search {
                    padding: 8px 16px;
                    background-color: #6c757d;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    text-decoration: none;
                }
                .clear-search:hover {
                    background-color: #5a6268;
                }
                /* Add star rating styling */
                .star-rating {
                    color: #ffd700;
                    font-size: 1.2em;
                }
            </style>
        </head>
        <body>
            <div class="search-container">
                <form class="search-form" method="GET">
                    <select name="filter" class="filter-select">
                        <option value="cs.service_title" <?php echo $current_filter === 'cs.service_title' ? 'selected' : ''; ?>>Service Title</option>
                        <option value="cs.service_type" <?php echo $current_filter === 'cs.service_type' ? 'selected' : ''; ?>>Service Type</option>
                        <option value="m.match_date" <?php echo $current_filter === 'm.match_date' ? 'selected' : ''; ?>>Match Date</option>
                        <option value="m.status" <?php echo $current_filter === 'm.status' ? 'selected' : ''; ?>>Status</option>
                        <option value="p.first_name" <?php echo $current_filter === 'p.first_name' ? 'selected' : ''; ?>>Homeowner First Name</option>
                        <option value="p.last_name" <?php echo $current_filter === 'p.last_name' ? 'selected' : ''; ?>>Homeowner Last Name</option>
                        <option value="m.rating" <?php echo $current_filter === 'm.rating' ? 'selected' : ''; ?>>Rating</option>
                    </select>
                    <input type="text" name="search" class="search-input" placeholder="Enter search term..." value="<?php echo $current_search; ?>">
                    <button type="submit" class="search-button">Search</button>
                    <?php if (!empty($current_search)): ?>
                        <a href="cleaner_view_matches.php" class="clear-search">Clear Search</a>
                    <?php endif; ?>
                </form>
            </div>
            <h2>Service Matches</h2>
            <table>
                <tr>
                    <th>Service Title</th>
                    <th>Service Type</th>
                    <th>Price</th>
                    <th>Homeowner</th>
                    <th>Contact Info</th>
                    <th>Match Date</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($matches as $match): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($match['service_title']); ?></td>
                        <td><?php echo htmlspecialchars($match['service_type']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($match['service_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($match['first_name'] . " " . $match['last_name']); ?></td>
                        <td>
                            Email: <?php echo htmlspecialchars($match['email']); ?><br>
                            Phone: <?php echo htmlspecialchars($match['phone_num']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                        <td class="status-<?php echo htmlspecialchars($match['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($match['status'])); ?>
                        </td>
                        <td>
                            <?php if ($match['status'] === 'rejected'): ?>
                                -
                            <?php elseif ($match['rating']): ?>
                                <span class="star-rating"><?php echo str_repeat('★', $match['rating']); ?></span>
                            <?php else: ?>
                                Not rated
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($match['status'] === 'rejected'): ?>
                                -
                            <?php else: ?>
                                <?php echo $match['review'] ? htmlspecialchars($match['review']) : 'No review yet'; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($match['status'] === 'pending'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="match_id" value="<?php echo $match['match_id']; ?>">
                                    <button type="submit" name="action" value="accept" class="accept-button">Accept</button>
                                    <button type="submit" name="action" value="reject" class="reject-button">Reject</button>
                                </form>
                            <?php else: ?>
                                <span>No actions available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <a href="cleaner_dashboard.php" class="return-button">Return to Dashboard</a>
        </body>
        </html>
        <?php
    }
}

// Main script
$database = new Database();
$conn = $database->getConnection();

$matchEntity = new MatchEntity($conn);
$controller = new ViewMatchesController($matchEntity);

// Handle match actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['match_id'])) {
    $controller->handleMatchAction($_POST['match_id'], $_POST['action']);
    header("Location: cleaner_view_matches.php");
    exit();
}

$page = new ViewMatchesPage($controller);
$page->ViewMatchesUI();

$database->closeConnection();
?>
