<?php
session_start();
require_once "../connectDatabase.php";

// Entity: CleaningService handles database operations
class CleaningService
{
    private $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllServices()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                cs.service_id,
                cs.service_title,
                sc.category as service_category,
                cs.service_price,
                cs.service_description,
                cs.views,
                u.username,
                p.first_name,
                p.last_name,
                COUNT(DISTINCT s.shortlist_id) as shortlist_count,
                COUNT(DISTINCT m.match_id) as match_count,
                AVG(m.rating) as avg_rating,
                sc.status_id
            FROM 
                cleaningservices cs
            JOIN 
                users u ON cs.cleaner_id = u.user_id
            JOIN 
                profile p ON u.user_id = p.user_id
            JOIN
                service_categories sc ON cs.service_category = sc.category_id
            LEFT JOIN
                shortlist s ON cs.service_id = s.service_id
            LEFT JOIN
                matches m ON cs.service_id = m.service_id
            GROUP BY
                cs.service_id, cs.service_title, sc.category, cs.service_price,
                cs.service_description, cs.views, u.username, p.first_name, p.last_name, sc.status_id
            ORDER BY cs.service_title ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
    }

    public function searchServices($criteria, $search)
    {
        $query = "
            SELECT 
                cs.service_id,
                cs.service_title,
                sc.category as service_category,
                cs.service_price,
                cs.service_description,
                cs.views,
                u.username,
                p.first_name,
                p.last_name,
                COUNT(DISTINCT s.shortlist_id) as shortlist_count,
                COUNT(DISTINCT m.match_id) as match_count,
                AVG(m.rating) as avg_rating,
                sc.status_id
            FROM 
                cleaningservices cs
            JOIN 
                users u ON cs.cleaner_id = u.user_id
            JOIN 
                profile p ON u.user_id = p.user_id
            JOIN
                service_categories sc ON cs.service_category = sc.category_id
            LEFT JOIN
                shortlist s ON cs.service_id = s.service_id
            LEFT JOIN
                matches m ON cs.service_id = m.service_id
        ";

        if ($criteria && $search) {
            if ($criteria === 'cleaner') {
                $query .= " WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE ? OR u.username LIKE ?";
                $query .= " GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price,
                            cs.service_description, cs.views, u.username, p.first_name, p.last_name, sc.status_id";
                $query .= " ORDER BY cs.service_title ASC";
                $stmt = $this->conn->prepare($query);
                $search = "%$search%";
                $stmt->bind_param("ss", $search, $search);
            } else if ($criteria === 'category') {
                $query .= " WHERE sc.category LIKE ?";
                $query .= " GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price,
                            cs.service_description, cs.views, u.username, p.first_name, p.last_name, sc.status_id";
                $query .= " ORDER BY sc.category ASC";
                $stmt = $this->conn->prepare($query);
                $search = "%$search%";
                $stmt->bind_param("s", $search);
            } else {
                $query .= " WHERE cs.$criteria LIKE ?";
                $query .= " GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price,
                            cs.service_description, cs.views, u.username, p.first_name, p.last_name, sc.status_id";
                $query .= " ORDER BY cs.$criteria ASC";
                $stmt = $this->conn->prepare($query);
                $search = "%$search%";
                $stmt->bind_param("s", $search);
            }
        } else {
            $query .= " GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price,
                        cs.service_description, cs.views, u.username, p.first_name, p.last_name, sc.status_id";
            $query .= " ORDER BY cs.service_title ASC";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
    }

    public function getServiceShortlists($service_id)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                s.shortlist_id,
                s.shortlist_date,
                u.username,
                p.first_name,
                p.last_name
            FROM 
                shortlist s
            JOIN 
                users u ON s.user_id = u.user_id
            JOIN 
                profile p ON u.user_id = p.user_id
            WHERE 
                s.service_id = ?
            ORDER BY 
                s.shortlist_date DESC
        ");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $shortlists = [];
        while ($row = $result->fetch_assoc()) {
            $shortlists[] = $row;
        }
        return $shortlists;
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
                h.username as homeowner_username,
                h.first_name as homeowner_first_name,
                h.last_name as homeowner_last_name,
                c.username as cleaner_username,
                c.first_name as cleaner_first_name,
                c.last_name as cleaner_last_name
            FROM 
                matches m
            JOIN 
                users u ON m.homeowner_id = u.user_id
            JOIN 
                profile h ON u.user_id = h.user_id
            JOIN 
                users v ON m.cleaner_id = v.user_id
            JOIN 
                profile c ON v.user_id = c.user_id
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
}

// Controller: Manages the service operations
class CleaningServiceController
{
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function getAllServices()
    {
        return $this->service->getAllServices();
    }

    public function searchServices($criteria, $search)
    {
        return $this->service->searchServices($criteria, $search);
    }

    public function getServiceShortlists($service_id)
    {
        return $this->service->getServiceShortlists($service_id);
    }

    public function getServiceMatches($service_id)
    {
        return $this->service->getServiceMatches($service_id);
    }
}

// Boundary: Manages the display of data
class CleaningServicePage
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function displayServices()
    {
        $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $searchCleaningService = isset($_POST['searchButton']);
        $viewShortlists = isset($_GET['view_shortlists']);
        $viewMatches = isset($_GET['view_matches']);
        $service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;

        if ($viewShortlists && $service_id) {
            $this->displayShortlists($service_id);
            return;
        }

        if ($viewMatches && $service_id) {
            $this->displayMatches($service_id);
            return;
        }

        if ($searchCleaningService) {
            $services = $this->controller->searchServices($criteria, $search);
        } else {
            $services = $this->controller->getAllServices();
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Platform Management Dashboard</title>
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
                    font-size: 1em;
                    display: inline-block;
                }
                .search-button:hover {
                    background-color: #0056b3;
                }
                .search-button.green, .search-button[style*='background-color: #28a745'] {
                    background-color: #28a745 !important;
                }
                .search-button.green:hover, .search-button[style*='background-color: #28a745']:hover {
                    background-color: #218838 !important;
                }

                .action-links {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }

                .action-links a {
                    color: #007bff;
                    text-decoration: none;
                }

                .action-links a:hover {
                    text-decoration: underline;
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

                .rating {
                    color: #ffc107;
                    font-weight: bold;
                }

                .review {
                    font-style: italic;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <h2>All Cleaning Services</h2>
            
            <form method="POST" action="pm_dashboard.php" class="search-form">
                <label for="service">Search based on:</label>
                <select id="service" name="criteria">
                    <option value="service_title">Service Title</option>
                    <option value="category">Category</option>
                    <option value="service_price">Price</option>
                    <option value="cleaner">Cleaner</option>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter Text Here" />
                <button class="search-button" type="submit" name="searchButton">Search</button>
                <a href="pm_view_reports.php" class="search-button" style="text-decoration: none; display: inline-block; background-color: #28a745;">View Reports</a>
            </form>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Service Title</th>
                            <th><a href="pm_view_service_categories.php" style="color: white; text-decoration: none;">Category <span style="color: #28a745;">(Manage)</span></a></th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Views</th>
                            <th>Cleaner</th>
                            <th>Shortlists</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr <?php echo ($service['status_id'] == 2) ? 'style="color: red;"' : ''; ?>>
                                <td><?php echo htmlspecialchars($service['service_title']); ?></td>
                                <td><?php echo htmlspecialchars($service['service_category']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($service['service_price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($service['service_description']); ?></td>
                                <td><?php echo htmlspecialchars($service['views']); ?></td>
                                <td><?php echo htmlspecialchars($service['first_name'] . ' ' . $service['last_name'] . ' (' . $service['username'] . ')'); ?></td>
                                <td><?php echo htmlspecialchars($service['shortlist_count']); ?></td>
                                <td class="action-links">
                                    <a href="pm_view_ratings.php?service_id=<?php echo $service['service_id']; ?>">View Ratings</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
    }

    private function displayShortlists($service_id)
    {
        $shortlists = $this->controller->getServiceShortlists($service_id);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Service Shortlists - clean.sg</title>
            <style>
                /* Reuse the same styles as above */
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

            <h2>Service Shortlists</h2>

            <div class="back-link">
                <a href="pm_dashboard.php">← Back to Dashboard</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Shortlist Date</th>
                            <th>User</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shortlists as $shortlist): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($shortlist['shortlist_date']); ?></td>
                                <td><?php echo htmlspecialchars($shortlist['username']); ?></td>
                                <td><?php echo htmlspecialchars($shortlist['first_name'] . ' ' . $shortlist['last_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
    }

    private function displayMatches($service_id)
    {
        $matches = $this->controller->getServiceMatches($service_id);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Service Matches - clean.sg</title>
            <style>
                /* Reuse the same styles as above */
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

                .rating {
                    color: #ffc107;
                    font-weight: bold;
                }

                .review {
                    font-style: italic;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <h2>Service Matches</h2>

            <div class="back-link">
                <a href="pm_dashboard.php">← Back to Dashboard</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Match Date</th>
                            <th>Status</th>
                            <th>Homeowner</th>
                            <th>Cleaner</th>
                            <th>Rating</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                                <td><?php echo htmlspecialchars($match['status']); ?></td>
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
                        <?php if (empty($matches)): ?>
                            <tr>
                                <td colspan="6">No matches found for this service.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$database = new Database();
$conn = $database->getConnection();

$cleaningService = new CleaningService($conn);
$controller = new CleaningServiceController($cleaningService);
$page = new CleaningServicePage($controller);

$page->displayServices();
?> 