<?php
require "../connectDatabase.php";
session_start();

// Entity classes
class CleaningServiceEntity {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getServicesByUsername($username) {
        $query = "SELECT cs.service_id, cs.service_title, sc.category as service_category, cs.service_price, cs.views,
                COUNT(s.shortlist_id) as shortlisted
                FROM cleaningservices cs
                LEFT JOIN shortlist s ON cs.service_id = s.service_id
                JOIN service_categories sc ON cs.service_category = sc.category_id
                WHERE cs.cleaner_id = (SELECT user_id FROM users WHERE username = ?)
                GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price, cs.views";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        $stmt->close();
        return $services;
    }

    public function searchCleaningService($username, $role, $search) {
        $query = "SELECT cs.service_id, cs.service_title, sc.category as service_category, cs.service_price, cs.views,
                COUNT(s.shortlist_id) as shortlisted 
                FROM cleaningservices cs
                LEFT JOIN shortlist s ON cs.service_id = s.service_id
                JOIN service_categories sc ON cs.service_category = sc.category_id
                WHERE cs.cleaner_id = (SELECT user_id FROM users WHERE username = ?)
                AND $role LIKE ?
                GROUP BY cs.service_id, cs.service_title, sc.category, cs.service_price, cs.views";
        $search = "%$search%";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $username, $search);
        $stmt->execute();

        $result = $stmt->get_result();
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        $stmt->close();
        return $services;
    }
}

class ShortlistEntity {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getShortlistedUsers($service_id) {
        $query = "SELECT s.shortlist_date, u.username, p.first_name, p.last_name, p.email, p.phone_num, p.gender, p.about
                  FROM shortlist s
                  JOIN users u ON s.user_id = u.user_id
                  JOIN profile p ON u.user_id = p.user_id
                  WHERE s.service_id = ?
                  ORDER BY s.shortlist_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $shortlistedUsers = [];
        while ($row = $result->fetch_assoc()) {
            $shortlistedUsers[] = $row;
        }
        $stmt->close();
        return $shortlistedUsers;
    }
}

// Controller classes
class CleaningServiceController {
    private $cleaningServiceEntity;
    private $username;

    public function __construct() {
        $this->cleaningServiceEntity = new CleaningServiceEntity();
        $this->username = $_SESSION['username'];
    }

    public function getServices() {
        return $this->cleaningServiceEntity->getServicesByUsername($this->username);
    }

    public function searchServices($role, $search) {
        return $this->cleaningServiceEntity->searchCleaningService($this->username, $role, $search);
    }
}

class ShortlistController {
    private $shortlistEntity;

    public function __construct() {
        $this->shortlistEntity = new ShortlistEntity();
    }

    public function getShortlistedUsers($service_id) {
        return $this->shortlistEntity->getShortlistedUsers($service_id);
    }
}

// Boundary class
class CleaningServicePage {
    private $controller;
    private $shortlistController;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->shortlistController = new ShortlistController();
    }

    public function handleFormSubmission() {
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        if (isset($_POST['searchButton'])) {
            $role = strtolower($_POST['service']);
            $search = $_POST['search'];
            return $this->controller->searchServices($role, $search);
        }

        if (isset($_POST['create'])) {
            header("Location: cleaner_create_listings.php");
            exit();
        }

        if (isset($_POST['view'])) {
            $service_id = $_POST['service_id'];
            header("Location: cleaner_service_details.php?service_id=" . urlencode($service_id));
            exit();
        }

        return $this->controller->getServices();
    }

    public function displayServices($services) {
        $username = $_SESSION['username'];
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>My Cleaning Services</title>
            <style>
                /* Basic Reset */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: Arial, sans-serif;
                }
                
                /* Body Styling */
                body {
                    background-color: #f8f9fa;
                    color: #343a40;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 20px;
                }
                
                /* Page Header */
                h2 {
                    margin-bottom: 20px;
                    font-size: 1.8em;
                    color: #007bff;
                }
                
                /* Form Styling */
                form {
                    margin-bottom: 15px;
                }

                /* Filter Form */
                .filter-form label {
                    margin-right: 10px;
                    font-weight: bold;
                }
                
                /* Search and Filter Input */
                select, input[type="text"] {
                    padding: 8px;
                    margin-right: 10px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                    font-size: 1em;
                }

                /* Buttons */
                button {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    font-size: 1em;
                    color: #ffffff;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                    margin: 0 5px;
                }

                /* Specific Button Colors */
                button[name="searchButton"] {
                    background-color: #007bff;
                }

                button[name="searchButton"]:hover {
                    background-color: #0056b3;
                }

                button[name="create"] {
                    background-color: #28a745;
                }

                button[name="create"]:hover {
                    background-color: #218838;
                }

                /* Table Styling */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    background-color: white;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    margin-top: 20px;
                }

                th, td {
                    padding: 12px;
                    text-align: center;
                    border: 1px solid #dee2e6;
                }

                th {
                    background-color: #6c757d;
                    color: #ffffff;
                    font-weight: bold;
                }

                tr:nth-child(even) {
                    background-color: #f1f1f1;
                }

                /* Action Buttons in Table */
                .action-buttons form {
                    display: inline;
                }

                .action-buttons button {
                    background-color: #007bff;
                    color: #ffffff;
                    margin: 0 3px;
                    padding: 6px 12px;
                }

                .action-buttons button:hover {
                    background-color: #0056b3;
                }

                .action-buttons .delete-button {
                    background-color: #dc3545;
                }

                .action-buttons .delete-button:hover {
                    background-color: #c82333;
                }

                .action-buttons .update-button {
                    background-color: #28a745;
                }

                .action-buttons .update-button:hover {
                    background-color: #218838;
                }

                /* Centering Content */
                .content {
                    max-width: 1000px;
                    width: 100%;
                }

                /* Return to Dashboard Button */
                .dashboard-button {
                    display: block;
                    margin: 20px auto;
                    padding: 10px 20px;
                    background-color: #6c757d;
                    color: #ffffff;
                    text-align: center;
                    border-radius: 5px;
                    font-size: 1.1em;
                    width: 25%;
                    text-decoration: none;
                }

                .dashboard-button:hover {
                    background-color: #5a6268;
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
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    z-index: 1001;
                }

                .popup-message {
                    margin-bottom: 20px;
                    font-size: 18px;
                    color: #333;
                }

                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }

                .popup-confirm {
                    background-color: #dc3545;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .popup-cancel {
                    background-color: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }

                /* Shortlist Popup Styles */
                .shortlist-popup-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                }

                .shortlist-popup-content {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    max-width: 80%;
                    max-height: 80vh;
                    overflow-y: auto;
                }

                .shortlist-popup-close {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    cursor: pointer;
                    font-size: 20px;
                }

                .shortlist-user-info {
                    margin-bottom: 15px;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }

                .shortlist-date {
                    color: #666;
                    font-size: 0.9em;
                }

                /* Centered Top Controls */
                .top-controls {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 2px;
                    margin-bottom: 15px;
                }
                .filter-form {
                    margin-bottom: 0;
                    display: flex;
                    align-items: center;
                    gap: 1px;
                }
                .create-form {
                    margin-bottom: 0;
                }
            </style>
        </head>
        <body>
            <!-- Add popup HTML at the start of body -->
            <div id="deletePopup" class="popup-overlay">
                <div class="popup-content">
                    <div class="popup-message">Are you sure you want to delete this service?</div>
                    <div class="popup-buttons">
                        <button id="confirmDelete" class="popup-confirm">Delete</button>
                        <button id="cancelDelete" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Add shortlist popup HTML -->
            <div id="shortlistPopup" class="shortlist-popup-overlay">
                <div class="shortlist-popup-content">
                    <span class="shortlist-popup-close">&times;</span>
                    <h3>Shortlisted Users</h3>
                    <div id="shortlistUsers"></div>
                </div>
            </div>

            <div class="content">
                <h2><?php echo htmlspecialchars($username); ?>'s Cleaning Services</h2>
                
                <!-- Centered Search and Create Button Container -->
                <div class="top-controls">
                    <form method="POST" action="" class="filter-form">
                        <label for="service">Search based on:</label>
                        <select id="service" name="service">
                            <option value="service_title">Service Title</option>
                            <option value="category">Category</option>
                            <option value="service_price">Price</option>
                        </select>
                        <input type="text" name="search" placeholder="Enter Text Here" />
                        <button type="submit" name="searchButton">Search</button>
                    </form>
                    <form method="post" action="cleaner_create_listings.php" class="create-form">
                        <button type="submit" name="create">Create new service</button>
                    </form>
                </div>

                <!-- Services Table -->
                <table>
                    <tr>
                        <th>Service Title</th>
                        <th>Service Category</th>
                        <th>Price</th>
                        <th>Views</th>
                        <th>Shortlisted</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['service_title']); ?></td>
                            <td><?php echo htmlspecialchars($service['service_category']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($service['service_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($service['views']); ?></td>
                            <td><?php echo htmlspecialchars($service['shortlisted']); ?></td>
                            <td class="action-buttons">
                                <form action="cleaner_service_details.php" method="get">
                                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                    <button type="submit">View</button>
                                </form>
                                <form action="cleaner_update_service_details.php" method="get">
                                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                    <button type="submit" class="update-button">Update</button>
                                </form>
                                <form action="cleaner_delete_service.php" method="get" class="delete-form">
                                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                    <button type="button" class="delete-button" onclick="showDeletePopup(this)">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Return to Dashboard Button -->
                <a href="cleaner_dashboard.php" class="dashboard-button">Return to Dashboard</a>
            </div>

            <!-- Add JavaScript at the end of body -->
            <script>
                function showDeletePopup(button) {
                    const popup = document.getElementById('deletePopup');
                    const form = button.closest('form');
                    
                    popup.style.display = 'block';

                    document.getElementById('confirmDelete').onclick = function() {
                        form.submit();
                    };

                    document.getElementById('cancelDelete').onclick = function() {
                        popup.style.display = 'none';
                    };

                    // Close popup when clicking outside
                    popup.onclick = function(e) {
                        if (e.target === popup) {
                            popup.style.display = 'none';
                        }
                    };
                }

                function showShortlistPopup(serviceId) {
                    fetch('get_shortlisted_users.php?service_id=' + serviceId)
                        .then(response => response.json())
                        .then(data => {
                            const popup = document.getElementById('shortlistPopup');
                            const usersContainer = document.getElementById('shortlistUsers');
                            usersContainer.innerHTML = '';

                            if (data.length === 0) {
                                usersContainer.innerHTML = '<p>No users have shortlisted this service.</p>';
                            } else {
                                data.forEach(user => {
                                    const userDiv = document.createElement('div');
                                    userDiv.className = 'shortlist-user-info';
                                    userDiv.innerHTML = `
                                        <p><strong>Name:</strong> ${user.first_name} ${user.last_name}</p>
                                        <p><strong>Username:</strong> ${user.username}</p>
                                        <p><strong>Email:</strong> ${user.email}</p>
                                        <p><strong>Phone:</strong> ${user.phone_num}</p>
                                        <p><strong>Gender:</strong> ${user.gender}</p>
                                        <p><strong>About:</strong> ${user.about}</p>
                                        <p class="shortlist-date">Shortlisted on: ${user.shortlist_date}</p>
                                    `;
                                    usersContainer.appendChild(userDiv);
                                });
                            }

                            popup.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while fetching shortlisted users.');
                        });
                }

                // Close popup when clicking the close button or outside the popup
                document.querySelector('.shortlist-popup-close').onclick = function() {
                    document.getElementById('shortlistPopup').style.display = 'none';
                };

                window.onclick = function(event) {
                    const popup = document.getElementById('shortlistPopup');
                    if (event.target === popup) {
                        popup.style.display = 'none';
                    }
                };
            </script>
        </body>
        </html>
        <?php
    }
}

// Main Script
$controller = new CleaningServiceController();
$page = new CleaningServicePage($controller);

$services = $page->handleFormSubmission();
$page->displayServices($services);
?>
