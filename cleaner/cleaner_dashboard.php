<?php
session_start();
require_once "../connectDatabase.php";

// Entity Layer
class UserEntity {
    private $db;
    private $username;

    public function __construct($username) {
        $this->db = (new Database())->getConnection();
        $this->username = htmlspecialchars($username);
    }

    public function getUsername() {
        return $this->username;
    }

    public function getUserRole() {
        $query = "SELECT r.role_name 
                 FROM users u 
                 JOIN role r ON u.role_id = r.role_id 
                 WHERE u.username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['role_name'] : null;
    }

    public function getServiceCount() {
        $query = "SELECT COUNT(*) as count 
                 FROM cleaningservices cs 
                 JOIN users u ON cs.cleaner_id = u.user_id 
                 WHERE u.username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['count'] : 0;
    }

    public function getMatchCount() {
        $query = "SELECT COUNT(*) as count 
                 FROM matches m 
                 JOIN cleaningservices cs ON m.service_id = cs.service_id 
                 JOIN users u ON cs.cleaner_id = u.user_id 
                 WHERE u.username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['count'] : 0;
    }
}

// Controller Layer
class DashboardController {
    private $userEntity;
    private $view;

    public function __construct($username) {
        $this->userEntity = new UserEntity($username);
        $this->view = new DashboardView();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['manageProfile'])) {
                header("Location: cleaner_manage_profile.php");
                exit();
            }

            if (isset($_POST['view'])) {
                header("Location: cleaner_view_listings.php");
                exit();
            }

            if (isset($_POST['viewMatches'])) {
                header("Location: cleaner_view_matches.php");
                exit();
            }

            if (isset($_POST['logout'])) {
                header("Location: ../logout.php");
                exit();
            }
        }

        $dashboardData = [
            'username' => $this->userEntity->getUsername(),
            'role' => $this->userEntity->getUserRole(),
            'serviceCount' => $this->userEntity->getServiceCount(),
            'matchCount' => $this->userEntity->getMatchCount()
        ];

        $this->view->render($dashboardData);
    }
}

// Boundary Layer
class DashboardView {
    public function render($data) {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cleaner Dashboard</title>
            <link rel="stylesheet" href="../style.css">
            <style>
                .dashboard-container {
                    max-width: 800px;
                    margin: 2rem auto;
                    padding: 0 1rem;
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

                .dashboard-header h2 {
                    color: var(--color-gray);
                    font-size: 1.25rem;
                    font-weight: 400;
                }

                .dashboard-stats {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1rem;
                    margin: 2rem 0;
                }

                .stat-card {
                    background-color: var(--color-white);
                    padding: 1.5rem;
                    border-radius: 0.5rem;
                    box-shadow: var(--shadow-sm);
                    text-align: center;
                }

                .stat-card h3 {
                    color: var(--color-gray);
                    font-size: 1rem;
                    margin-bottom: 0.5rem;
                }

                .stat-card p {
                    color: var(--color-black);
                    font-size: 2rem;
                    font-weight: bold;
                }

                .dashboard-actions {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                    margin-top: 2rem;
                }

                .action-button {
                    background-color: var(--color-white);
                    padding: 1.5rem;
                    border-radius: 0.5rem;
                    box-shadow: var(--shadow-sm);
                    text-align: center;
                    cursor: pointer;
                    transition: transform 0.2s, background-color 0.2s;
                    border: none;
                    width: 100%;
                }

                .action-button:hover {
                    transform: translateY(-2px);
                }

                .action-button h3 {
                    color: var(--color-black);
                    margin-bottom: 0.5rem;
                }

                .action-button p {
                    color: var(--color-gray);
                    font-size: 0.875rem;
                }

                /* Button specific colors */
                button[name="manageProfile"] {
                    background-color: #4CAF50;
                    color: white;
                }

                button[name="manageProfile"]:hover {
                    background-color: #45a049;
                }

                button[name="manageProfile"] h3,
                button[name="manageProfile"] p {
                    color: white;
                }

                button[name="view"] {
                    background-color: #2196F3;
                    color: white;
                }

                button[name="view"]:hover {
                    background-color: #1e88e5;
                }

                button[name="view"] h3,
                button[name="view"] p {
                    color: white;
                }

                button[name="viewMatches"] {
                    background-color: #9C27B0;
                    color: white;
                }

                button[name="viewMatches"]:hover {
                    background-color: #8e24aa;
                }

                button[name="viewMatches"] h3,
                button[name="viewMatches"] p {
                    color: white;
                }

                button[name="logout"] {
                    background-color: #f44336;
                    color: white;
                }

                button[name="logout"]:hover {
                    background-color: #e53935;
                }

                button[name="logout"] h3,
                button[name="logout"] p {
                    color: white;
                }
            </style>
        </head>
        <body>
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h1>Welcome, <?php echo htmlspecialchars($data['username']); ?>!</h1>
                    <h2><?php echo htmlspecialchars($data['role']); ?> Dashboard</h2>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Total Services</h3>
                        <p><?php echo $data['serviceCount']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Matches</h3>
                        <p><?php echo $data['matchCount']; ?></p>
                    </div>
                </div>

                <div class="dashboard-actions">
                    <form method="POST">
                        <button type="submit" name="manageProfile" class="action-button">
                            <h3>Manage Profile</h3>
                            <p>Update your personal information and preferences</p>
                        </button>
                    </form>

                    <form method="POST">
                        <button type="submit" name="view" class="action-button">
                            <h3>View Listings</h3>
                            <p>Manage your cleaning service listings</p>
                        </button>
                    </form>

                    <form method="POST">
                        <button type="submit" name="viewMatches" class="action-button">
                            <h3>View Matches</h3>
                            <p>View and manage your service matches</p>
                        </button>
                    </form>

                    <form method="POST">
                        <button type="submit" name="logout" class="action-button">
                            <h3>Logout</h3>
                            <p>Sign out of your account</p>
                        </button>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$controller = new DashboardController($_SESSION['username']);
$controller->handleRequest();
?>
