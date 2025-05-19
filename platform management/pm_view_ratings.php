<?php
session_start();
require_once "../connectDatabase.php";

// Entity: MatchEntity handles database operations
class MatchEntity
{
    private $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getServiceMatches($service_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                m.match_id,
                m.match_date,
                m.status,
                m.rating,
                m.review,
                m.homeowner_id,
                ho.username as homeowner_username,
                ho_p.first_name as homeowner_first_name,
                ho_p.last_name as homeowner_last_name
            FROM 
                matches m
            JOIN 
                users ho ON m.homeowner_id = ho.user_id
            JOIN 
                profile ho_p ON ho.user_id = ho_p.user_id
            WHERE 
                m.service_id = ?
            ORDER BY 
                m.match_date DESC
        ");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }

    public function getServiceDetails($service_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                cs.service_title,
                sc.category as service_category,
                cs.service_price,
                u.username,
                p.first_name,
                p.last_name
            FROM 
                cleaningservices cs
            JOIN 
                users u ON cs.cleaner_id = u.user_id
            JOIN 
                profile p ON u.user_id = p.user_id
            JOIN
                service_categories sc ON cs.service_category = sc.category_id
            WHERE 
                cs.service_id = ?
        ");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

// Controller: Manages the match operations
class MatchController
{
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function getServiceMatches($service_id)
    {
        return $this->entity->getServiceMatches($service_id);
    }

    public function getServiceDetails($service_id)
    {
        return $this->entity->getServiceDetails($service_id);
    }
}

// Boundary: Manages the display of match data
class MatchPage
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function displayMatches()
    {
        if (!isset($_GET['service_id'])) {
            header("Location: pm_dashboard.php");
            exit();
        }

        $service_id = $_GET['service_id'];
        $matches = $this->controller->getServiceMatches($service_id);
        $service = $this->controller->getServiceDetails($service_id);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Service Ratings - clean.sg</title>
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

                .service-info {
                    background-color: white;
                    padding: 20px;
                    margin: 20px auto;
                    width: 90%;
                    border-radius: 5px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .service-info p {
                    margin: 5px 0;
                    color: #343a40;
                }

                .service-info .title {
                    font-size: 1.2em;
                    font-weight: bold;
                    color: #007bff;
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

                .table-container {
                    margin-bottom: 40px; /* Add more bottom space for the table */
                }

                .back-link {
                    display: block;
                    text-align: center;
                    margin: 40px 0; /* Increased margin to move button lower */
                }

                .back-link a {
                    text-decoration: none;
                    color: #ffffff;
                    background-color: #6c757d;
                    padding: 10px 20px;
                    border-radius: 4px;
                    font-size: 1em;
                }

                .back-link a:hover {
                    background-color: #5a6268;
                }

                .rating {
                    color: #ffc107;
                    font-weight: bold;
                    font-size: 1.2em;
                }

                .review {
                    text-align: left;
                    padding: 10px;
                    background-color: #f8f9fa;
                    border-radius: 4px;
                    margin-top: 5px;
                }

                .homeowner-id {
                    font-family: monospace;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <h2>Service Ratings</h2>

            <div class="service-info">
                <p class="title"><?php echo htmlspecialchars($service['service_title']); ?></p>
                <p>Type: <?php echo htmlspecialchars($service['service_category']); ?></p>
                <p>Price: $<?php echo htmlspecialchars(number_format($service['service_price'], 2)); ?></p>
                <p>Cleaner: <?php echo htmlspecialchars($service['first_name'] . ' ' . $service['last_name'] . ' (' . $service['username'] . ')'); ?></p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Homeowner</th>
                            <th>Rating</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                                <td><?php echo htmlspecialchars($match['homeowner_first_name'] . ' ' . $match['homeowner_last_name'] . ' (' . $match['homeowner_username'] . ')'); ?></td>
                                <td>
                                    <?php if ($match['rating']): ?>
                                        <span class="rating"><?php echo htmlspecialchars($match['rating']); ?>/5</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($match['review']): ?>
                                        <div class="review"><?php echo nl2br(htmlspecialchars($match['review'])); ?></div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($matches)): ?>
                            <tr>
                                <td colspan="4">No ratings found for this service.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="back-link">
                <a href="pm_dashboard.php">Back to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$database = new Database();
$conn = $database->getConnection();

$matchEntity = new MatchEntity($conn);
$controller = new MatchController($matchEntity);
$page = new MatchPage($controller);

$page->displayMatches();
?>
