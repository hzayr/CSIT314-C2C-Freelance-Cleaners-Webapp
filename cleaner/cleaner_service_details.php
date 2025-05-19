<?php
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

/** ENTITY CLASS */
class CleaningService {
    private $db;

    public function __construct() {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1";
            $username = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') ?: "";
            $dbname = getenv('DB_NAME') ?: "csit314";
            $port = getenv('DB_PORT') ?: 3307;
    
            $dsn = "mysql:host={$servername};port={$port};dbname={$dbname}";
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getServiceDetails($service_id, $username) {
        // First verify that this service belongs to the logged-in user
        $stmt = $this->db->prepare("
            SELECT cs.*, u.username 
            FROM cleaningservices cs
            JOIN users u ON cs.cleaner_id = u.user_id
            WHERE cs.service_id = ? AND u.username = ?
        ");
        $stmt->execute([$service_id, $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementViews($service_id) {
        $stmt = $this->db->prepare("
            UPDATE cleaningservices 
            SET views = views + 1 
            WHERE service_id = ?
        ");
        return $stmt->execute([$service_id]);
    }

    public function getShortlistedUsers($service_id) {
        $query = "SELECT s.shortlist_date, u.username, u.email, u.phone_num, 
                         p.first_name, p.last_name, p.gender, p.about
                  FROM shortlist s
                  JOIN users u ON s.user_id = u.user_id
                  JOIN profile p ON u.user_id = p.user_id
                  WHERE s.service_id = ?
                  ORDER BY s.shortlist_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$service_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getShortlistedCount($service_id) {
        $query = "SELECT COUNT(*) as count FROM shortlist WHERE service_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$service_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}

/** CONTROLLER CLASS */
class ServiceDetailsController {
    private $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    private function isServiceOwner($service_id, $username) {
        $service = $this->entity->getServiceDetails($service_id, $username);
        return $service !== false;
    }

    public function getServiceDetails($service_id, $username) {
        $service = $this->entity->getServiceDetails($service_id, $username);
        if ($service) {
            // Only increment views if the current user is not the owner
            if (!$this->isServiceOwner($service_id, $_SESSION['username'])) {
                $this->entity->incrementViews($service_id);
            }
        }
        return $service;
    }

    public function getShortlistedUsers($service_id) {
        return $this->entity->getShortlistedUsers($service_id);
    }

    public function getShortlistedCount($service_id) {
        return $this->entity->getShortlistedCount($service_id);
    }
}

/** BOUNDARY CLASS */
class ServiceDetailsPage {
    private $controller;
    private $message;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->message = "";
    }

    public function displayServiceDetails() {
        if (!isset($_GET['service_id'])) {
            $this->message = "No service ID provided.";
            return;
        }

        $service = $this->controller->getServiceDetails($_GET['service_id'], $_SESSION['username']);
        $shortlistedUsers = $this->controller->getShortlistedUsers($_GET['service_id']);
        $shortlistedCount = $this->controller->getShortlistedCount($_GET['service_id']);

        if (!$service) {
            $this->message = "Service not found or access denied.";
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Service Details</title>
            <link rel="stylesheet" href="../style.css">
            <style>
                body {
                    overflow-y: auto;
                    min-height: 100vh;
                    padding-top: 4rem;
                }

                .dashboard-container {
                    max-width: 800px;
                    margin: 2rem auto;
                    padding: 0 1rem;
                    overflow-y: auto;
                }

                .dashboard-header {
                    background-color: var(--color-white);
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-md);
                    margin-bottom: 2rem;
                    text-align: center;
                }

                .dashboard-header h1 {
                    color: var(--color-black);
                    font-size: 1.875rem;
                    margin-bottom: 0.5rem;
                }

                .service-details {
                    background-color: var(--color-white);
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-md);
                    margin-bottom: 2rem;
                }

                .detail-row {
                    display: flex;
                    border-bottom: 1px solid var(--color-gray-light);
                    padding: 1rem 0;
                }

                .detail-label {
                    width: 200px;
                    font-weight: bold;
                    color: var(--color-gray);
                }

                .detail-value {
                    flex: 1;
                    color: var(--color-black);
                }

                .stats {
                    display: flex;
                    justify-content: space-around;
                    margin: 2rem 0;
                    padding: 1.5rem;
                    background-color: var(--color-gray-light);
                    border-radius: 0.75rem;
                }

                .stat-item {
                    text-align: center;
                }

                .stat-value {
                    font-size: 1.5rem;
                    font-weight: bold;
                    color: var(--color-primary);
                }

                .stat-label {
                    color: var(--color-gray);
                    font-size: 0.875rem;
                }

                .button-group {
                    display: flex;
                    justify-content: center;
                    gap: 1rem;
                    margin-top: 2rem;
                }

                .button {
                    background-color: var(--color-white);
                    border: none;
                    padding: 1rem 2rem;
                    border-radius: 0.75rem;
                    box-shadow: var(--shadow-md);
                    cursor: pointer;
                    transition: all 0.2s;
                    text-align: center;
                    color: var(--color-black);
                    font-size: 1.125rem;
                    font-weight: 500;
                    text-decoration: none;
                }

                .button:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--shadow-lg);
                }

                .edit-button {
                    background-color: var(--color-primary);
                    color: var(--color-white);
                }

                .delete-button {
                    background-color: #fee2e2;
                    color: #dc2626;
                }

                .delete-button:hover {
                    background-color: #fecaca;
                }

                .back-button {
                    background-color: var(--color-gray);
                    color: var(--color-white);
                }

                .message {
                    text-align: center;
                    padding: 1rem;
                    margin: 1rem 0;
                    border-radius: 0.5rem;
                }

                .error {
                    background-color: #fee2e2;
                    color: #dc2626;
                    border: 1px solid #fecaca;
                }

                .success {
                    background-color: #dcfce7;
                    color: #16a34a;
                    border: 1px solid #bbf7d0;
                }

                .shortlisted-users {
                    background-color: var(--color-white);
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-md);
                    margin-top: 2rem;
                }

                .user-card {
                    border: 1px solid var(--color-gray-light);
                    border-radius: 0.75rem;
                    padding: 1.5rem;
                    margin-bottom: 1.5rem;
                    background-color: var(--color-white);
                }

                .user-card h3 {
                    color: var(--color-primary);
                    margin-top: 0;
                }

                .user-info {
                    margin-bottom: 1rem;
                }

                .user-info strong {
                    color: var(--color-gray);
                }

                .shortlist-date {
                    color: var(--color-gray);
                    font-size: 0.875rem;
                    margin-top: 1rem;
                }

                .no-users {
                    text-align: center;
                    color: var(--color-gray);
                    padding: 2rem;
                }

                .logo {
                    position: fixed;
                    top: 1rem;
                    left: 1rem;
                    z-index: 1000;
                    background-color: var(--color-white);
                    padding: 0.5rem 1rem;
                    border-radius: 0.5rem;
                    box-shadow: var(--shadow-md);
                }

                .logo h3 {
                    color: var(--color-primary);
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin: 0;
                }

                @media (max-width: 640px) {
                    .button-group {
                        flex-direction: column;
                    }

                    .button {
                        width: 100%;
                    }
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
                    background-color: var(--color-white);
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-lg);
                    text-align: center;
                    z-index: 1001;
                }

                .popup-message {
                    margin-bottom: 1.5rem;
                    font-size: 1.125rem;
                    color: var(--color-black);
                }

                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 1rem;
                }

                .popup-confirm {
                    background-color: #dc3545;
                    color: var(--color-white);
                    border: none;
                    padding: 0.75rem 1.5rem;
                    border-radius: 0.5rem;
                    cursor: pointer;
                    font-size: 1rem;
                }

                .popup-confirm:hover {
                    background-color: #c82333;
                }

                .popup-cancel {
                    background-color: var(--color-gray);
                    color: var(--color-white);
                    border: none;
                    padding: 0.75rem 1.5rem;
                    border-radius: 0.5rem;
                    cursor: pointer;
                    font-size: 1rem;
                }

                .popup-cancel:hover {
                    background-color: #5a6268;
                }
            </style>
        </head>
        <body>
            <a href="../index.html" class="logo"><h3>clean.sg</h3></a>

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

            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h1>Service Details</h1>
                </div>

                <?php if (!empty($this->message)): ?>
                    <div class="message <?php echo strpos($this->message, 'successfully') !== false ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($this->message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($service): ?>
                    <div class="service-details">
                        <div class="detail-row">
                            <div class="detail-label">Service Title</div>
                            <div class="detail-value"><?php echo htmlspecialchars($service['service_title']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Service Category</div>
                            <div class="detail-value"><?php echo htmlspecialchars($service['service_category']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Description</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($service['service_description'])); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Price</div>
                            <div class="detail-value">$<?php echo htmlspecialchars(number_format($service['service_price'], 2)); ?></div>
                        </div>
                    </div>

                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo htmlspecialchars($service['views']); ?></div>
                            <div class="stat-label">Views</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo htmlspecialchars($shortlistedCount); ?></div>
                            <div class="stat-label">Times Shortlisted</div>
                        </div>
                    </div>

                    <div class="shortlisted-users">
                        <h2>Shortlisted Users</h2>
                        <?php if (empty($shortlistedUsers)): ?>
                            <div class="no-users">
                                <p>No users have shortlisted this service yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($shortlistedUsers as $user): ?>
                                <div class="user-card">
                                    <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                                    <div class="user-info">
                                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_num']); ?></p>
                                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                                        <p><strong>About:</strong> <?php echo htmlspecialchars($user['about']); ?></p>
                                    </div>
                                    <div class="shortlist-date">
                                        Shortlisted on: <?php echo htmlspecialchars($user['shortlist_date']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="button-group">
                        <a href="cleaner_update_service_details.php?service_id=<?php echo urlencode($service['service_id']); ?>" class="button edit-button">Update Service</a>
                        <form id="deleteForm" action="cleaner_delete_service.php" method="get" style="display: inline;">
                            <input type="hidden" name="service_id" value="<?php echo urlencode($service['service_id']); ?>">
                            <button type="button" class="button delete-button" onclick="showDeletePopup()">Delete Service</button>
                        </form>
                        <a href="cleaner_view_listings.php" class="button back-button">Return to My Services</a>
                    </div>
                <?php endif; ?>
            </div>

            <script>
                function showDeletePopup() {
                    const popup = document.getElementById('deletePopup');
                    const form = document.getElementById('deleteForm');
                    
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
            </script>
        </body>
        </html>
        <?php
    }
}

// Main Script
$cleaningServiceEntity = new CleaningService();
$serviceDetailsController = new ServiceDetailsController($cleaningServiceEntity);
$serviceDetailsPage = new ServiceDetailsPage($serviceDetailsController);
$serviceDetailsPage->displayServiceDetails();
?> 