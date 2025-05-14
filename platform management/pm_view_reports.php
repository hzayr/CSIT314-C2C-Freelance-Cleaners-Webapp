<?php
session_start();
require_once "../connectDatabase.php";

// Entity: Handles DB operations for reports
class ReportEntity {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function getMatchesByDate($date) {
        $stmt = $this->conn->prepare("SELECT m.*, u.username as homeowner_username, p.first_name as homeowner_first_name, p.last_name as homeowner_last_name, v.username as cleaner_username, q.first_name as cleaner_first_name, q.last_name as cleaner_last_name, cs.service_title FROM matches m JOIN users u ON m.homeowner_id = u.user_id JOIN profile p ON u.user_id = p.user_id JOIN users v ON m.cleaner_id = v.user_id JOIN profile q ON v.user_id = q.user_id JOIN cleaningservices cs ON m.service_id = cs.service_id WHERE DATE(m.match_date) = ? ORDER BY m.match_date DESC");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        $stmt->close();
        return $matches;
    }
}

// Control: Mediates between entity and boundary
class ReportController {
    private $entity;
    public function __construct($entity) {
        $this->entity = $entity;
    }
    public function getMatchesByDate($date) {
        return $this->entity->getMatchesByDate($date);
    }
}

// Boundary: Handles UI
class ReportPage {
    private $controller;
    public function __construct($controller) {
        $this->controller = $controller;
    }
    public function displayPage() {
        header('Content-Type: text/html; charset=UTF-8');
        $selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $matches = $this->controller->getMatchesByDate($selected_date);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daily Reports - clean.sg</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }
                header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    background-color: #343a40;
                    color: #ffffff;
                }
                header h1 {
                    margin: 0;
                    font-size: 1.5em;
                }
                header a {
                    text-decoration: none;
                    color: #ffffff;
                    background-color: #007bff;
                    padding: 8px 16px;
                    border-radius: 4px;
                    font-size: 0.9em;
                }
                header a[href="../logout.php"] {
                    background-color: #dc3545;
                }
                header a:hover {
                    background-color: #0056b3;
                }
                header a[href="../logout.php"]:hover {
                    background-color: #c82333;
                }
                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-top: 20px;
                }
                .date-form {
                    text-align: center;
                    margin: 20px 0;
                }
                .date-form input[type="date"] {
                    padding: 8px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                }
                .date-form button {
                    background-color: #007bff;
                    color: white;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                    margin-left: 10px;
                }
                .date-form button:hover {
                    background-color: #0056b3;
                }
                table {
                    width: 90%;
                    margin: 20px auto;
                    border-collapse: collapse;
                    background-color: white;
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
                .rating {
                    color: #ffc107;
                    font-weight: bold;
                }
                .review {
                    font-style: italic;
                    color: #6c757d;
                }
                .back-link {
                    display: block;
                    text-align: center;
                    margin: 20px 0;
                }
                .back-link a {
                    color: #007bff;
                    text-decoration: none;
                }
                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>
            <h2>Daily Reports</h2>
            <form class="date-form" method="get" action="pm_view_reports.php">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" max="<?php echo date('Y-m-d'); ?>">
                <button type="submit">View</button>
            </form>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Match Date</th>
                            <th>Status</th>
                            <th>Service Title</th>
                            <th>Homeowner</th>
                            <th>Cleaner</th>
                            <th>Rating</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($matches)): ?>
                            <tr><td colspan="6">No matches found for this date.</td></tr>
                        <?php else: ?>
                            <?php foreach ($matches as $match): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                                    <td><?php echo htmlspecialchars($match['status']); ?></td>
                                    <td><?php echo htmlspecialchars($match['service_title']); ?></td>
                                    <td><?php echo htmlspecialchars($match['homeowner_first_name'] . ' ' . $match['homeowner_last_name'] . ' (' . $match['homeowner_username'] . ')'); ?></td>
                                    <td><?php echo htmlspecialchars($match['cleaner_first_name'] . ' ' . $match['cleaner_last_name'] . ' (' . $match['cleaner_username'] . ')'); ?></td>
                                    <td>
                                        <?php if ($match['rating']): ?>
                                            <span class="rating"><?php echo htmlspecialchars($match['rating']); ?>/5</span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($match['review']): ?>
                                            <span class="review"><?php echo htmlspecialchars($match['review']); ?></span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="back-link">
                <a href="pm_dashboard.php">← Back to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$database = new Database();
$conn = $database->getConnection();
$conn->set_charset("utf8mb4");

$entity = new ReportEntity($conn);
$controller = new ReportController($entity);
$page = new ReportPage($controller);
$page->displayPage();
