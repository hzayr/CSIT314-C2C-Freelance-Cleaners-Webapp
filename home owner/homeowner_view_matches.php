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

    public function getMatchesByHomeowner($homeowner_id)
    {
        $stmt = $this->conn->prepare("
            SELECT m.match_id, m.service_id, m.match_date, m.status,
                   cs.service_title, cs.service_type, cs.service_price,
                   p.first_name, p.last_name
            FROM matches m
            JOIN cleaningservices cs ON m.service_id = cs.service_id
            JOIN profile p ON m.cleaner_id = p.user_id
            WHERE m.homeowner_id = ?
            ORDER BY m.match_date DESC
        ");
        $stmt->bind_param("i", $homeowner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }

    public function searchMatches($homeowner_id, $criteria, $search)
    {
        $stmt = $this->conn->prepare("
            SELECT m.match_id, m.service_id, m.match_date, m.status,
                   cs.service_title, cs.service_type, cs.service_price,
                   p.first_name, p.last_name
            FROM matches m
            JOIN cleaningservices cs ON m.service_id = cs.service_id
            JOIN profile p ON m.cleaner_id = p.user_id
            WHERE m.homeowner_id = ?
            AND (
                cs.service_title LIKE ? OR
                cs.service_type LIKE ? OR
                cs.service_price LIKE ? OR
                p.first_name LIKE ? OR
                p.last_name LIKE ? OR
                m.status LIKE ?
            )
            ORDER BY m.match_date DESC
        ");
        $stmt->bind_param("issssss", $homeowner_id, $search, $search, $search, $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }
}

class ViewMatchesController
{
    private $matchEntity;

    public function __construct($matchEntity)
    {
        $this->matchEntity = $matchEntity;
    }

    public function getHomeownerID()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getMatches()
    {
        $homeownerID = $this->getHomeownerID();
        return $homeownerID !== null ? $this->matchEntity->getMatchesByHomeowner($homeownerID) : [];
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
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>My Matches</title>
            <meta charset="UTF-8">
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
                .return-button {
                    display: inline-block;
                    margin: 20px;
                    padding: 10px 15px;
                    background-color: #6c757d;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .return-button:hover {
                    background-color: #5a6268;
                }
                .view-button {
                    display: inline-block;
                    margin: 20px;
                    padding: 10px 15px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .view-button:hover {
                    background-color: #0056b3;
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
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
            </header>
            <h2>My Service Matches</h2>

            <form method="POST" action="homeowner_view_matches.php" class="search-form">
                <label for="service">Search based on:</label>
                <select id="service" name="criteria">
                    <option value="service_title">Service Title</option>
                    <option value="service_type">Service Type</option>
                    <option value="service_price">Price</option>
                    <option value="first_name">Cleaner First Name</option>
                    <option value="last_name">Cleaner Last Name</option>
                    <option value="status">Status</option>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter Text Here" />
                <button class="search-button" type="submit" name="searchButton">Search</button>
            </form>

            <table>
                <tr>
                    <th>Service Title</th>
                    <th>Service Type</th>
                    <th>Price</th>
                    <th>Cleaner</th>
                    <th>Match Date</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($matches as $match): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($match['service_title']); ?></td>
                        <td><?php echo htmlspecialchars($match['service_type']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($match['service_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($match['first_name'] . " " . $match['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                        <td class="status-<?php echo htmlspecialchars($match['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($match['status'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div style="text-align: center; margin: 20px;">
                <a href="homeowner_view_shortlist.php" class="view-button">View Shortlist</a>
                <a href="homeowner_view_history.php" class="view-button">View History</a>
                <br>
                <a href="homeowner_dashboard.php" class="return-button">Return to Main Page</a>
            </div>
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
$page = new ViewMatchesPage($controller);

// Handle search functionality
if (isset($_POST['searchButton'])) {
    $criteria = $_POST['criteria'] ?? '';
    $search = $_POST['search'] ?? '';
    $matches = $matchEntity->searchMatches($controller->getHomeownerID(), $criteria, $search);
} else {
    $matches = $controller->getMatches();
}

$page->ViewMatchesUI();

$database->closeConnection();
?> 