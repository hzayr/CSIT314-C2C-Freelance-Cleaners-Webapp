<?php
session_start();

// BOUNDARY LAYER: Responsible for rendering user information and handling requests
class SuspendUserProfilePage {
    private $controller;
    private $profileData; // Store profile data internally

    public function __construct($controller) {
        $this->controller = $controller;
    }

    // This method will output the CSS styles for the page
    private function renderStyles() {
        ?>
        <style>
            body {
                font-family: 'Poppins', 'Arial', sans-serif;
                background-color: #0d1117;
                color: #c9d1d9;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            h1 {
                text-align: center;
                color: #58a6ff;
                margin: 40px 0 20px 0;
                font-weight: 600;
            }
            #infoTable {
                border-collapse: collapse;
                width: 90%;
                max-width: 1000px;
                margin: 20px auto;
                background-color: #161b22;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0,0,0,0.7);
            }
            #infoTable th, td {
                padding: 14px 18px;
                text-align: left;
                font-size: 16px;
                border-bottom: 1px solid #21262d;
            }
            #infoTable th {
                background-color: #21262d;
                color: #58a6ff;
                font-weight: 600;
                font-size: 1rem;
                width: 30%;
            }
            #infoTable td {
                color: #c9d1d9;
            }
            #infoTable tr:hover {
                background-color: #30363d;
            }
            button {
                font-size: 14px;
                padding: 8px 16px;
                margin: 2px;
                border-radius: 6px;
                border: none;
                cursor: pointer;
                transition: background 0.3s;
            }
            .suspend-btn, .remove-suspend-btn {
                background-color: #da3633;
                color: white;
            }
            .suspend-btn:hover, .remove-suspend-btn:hover {
                background-color: #f85149;
            }
            .return-btn {
                background-color:hsl(0, 0.00%, 50%);
                color: white;
            }
            .return-btn:hover {
                background-color:hsl(0, 0.00%, 50%);
            }
            .form-body {
                text-align: center;
            }
            @media (max-width: 768px) {
                #infoTable {
                    width: 95%;
                }
                h1 {
                    font-size: 1.8rem;
                }
            }
            .button-container {
                display: flex;
                flex-direction: column;
                gap: 20px;
                width: 100%;
                max-width: 300px;
                margin: 20px auto;
            }
            .button-cell {
                padding: 20px;
            }
        </style>
        <?php
    }

    public function SuspendUserProfileUI() {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Suspend Confirmation</title>
            <?php $this->renderStyles(); ?> <!-- Call the renderStyles method to inject CSS -->
        </head>
        <body>
            <h1>Suspend this role?</h1>
            <table id="infoTable">
                <tr>
                    <th>Profile</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->profileData['role_name'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Number of Accounts</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->profileData['account_count'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->profileData['status_name']); ?></td>
                </tr>
            </table>
            <div class="button-container">
                <?php if ($this->profileData['status_name'] === 'Active'): ?>
                    <form action="" method="POST" class="form-body"> 
                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($this->profileData['role_id']); ?>">
                        <input type="hidden" name="action" value="suspend">
                        <button type="submit" class="suspend-btn">Suspend</button>
                    </form>
                <?php elseif ($this->profileData['status_name'] === 'Suspended'): ?>
                    <form action="" method="POST" class="form-body"> 
                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($this->profileData['role_id']); ?>">
                        <input type="hidden" name="action" value="Remove">
                        <button type="submit" class="remove-suspend-btn">Remove Suspension</button>
                    </form>
                <?php endif; ?>
                <form action="admin_manage_user_profiles.php" class="form-body">
                    <button type="submit" class="return-btn">Return</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }

    public function handleRequest() {
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        // Use GET parameter to fetch the role_id
        $role_id = $_GET['role_id'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $role_id = $_POST['role_id'] ?? '';

            if ($action === 'suspend') {
                $this->controller->suspendUserProfile($role_id);
                $_SESSION['success_message'] = "Profile suspended successfully.";
                echo "<script>
                    alert('" . htmlspecialchars($_SESSION['success_message']) . "');
                    window.location.href = 'admin_manage_user_profiles.php';
                </script>";
                exit();
            }

            if ($action === 'Remove') {
                $this->controller->setRemoveSuspend($role_id);
                $_SESSION['removeSuspend_message'] = "Profile suspension removed.";
                echo "<script>
                    alert('" . htmlspecialchars($_SESSION['removeSuspend_message']) . "');
                    window.location.href = 'admin_manage_user_profiles.php';
                </script>";
                exit();
            }
        }

        if ($role_id) {
            // Retrieve profile data and store it in the property
            $this->profileData = $this->controller->getProfile($role_id);
            $this->SuspendUserProfileUI();
        } else {
            echo "No profile provided.";
        }
    }
}

// CONTROL LAYER: Serves as an intermediary between view and entity
class SuspendUserProfileController {
    private $userAccountModel;

    public function __construct() {
        $this->userAccountModel = new UserProfile();
    }

    public function getProfile($role_id) {
        return $this->userAccountModel->getProfileByRole($role_id);
    }

    public function suspendUserProfile($role_id) {
        return $this->userAccountModel->suspendUserProfile($role_id);
    }

    public function setRemoveSuspend($role_id) {
        return $this->userAccountModel->removeSuspend($role_id);
    }
}

// ENTITY: Handles all logic for user data and database interactions
class UserProfile {
    private $pdo;

    public function __construct() {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1";
            $username = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') ?: "";
            $dbname = getenv('DB_NAME') ?: "csit314";
            $port = getenv('DB_PORT') ?: 3307;

            $dsn = "mysql:host=$servername;port=$port;dbname=$dbname";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getProfileByRole($role_id) {
        $stmt = $this->pdo->prepare("SELECT r.role_id, r.role_name, s.status_name, COUNT(*) AS account_count
            FROM users u
            JOIN role r ON r.role_id = u.role_id
            JOIN status s ON s.status_id = u.status_id
            WHERE u.role_id = :role_id
            GROUP BY u.role_id
            ORDER BY u.role_id ASC");

        $stmt->bindParam(':role_id', $role_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function suspendUserProfile($role_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET status_id = 2 WHERE role_id = :role_id");
        $stmt->bindParam(':role_id', $role_id);
        $stmt->execute();
    }

    public function removeSuspend($role_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET status_id = 1 WHERE role_id = :role_id");
        $stmt->bindParam(':role_id', $role_id);
        $stmt->execute();
    }
}

// MAIN EXECUTION: Initialize and handle the request in the Boundary layer
$accountController = new SuspendUserProfileController();
$profileView = new SuspendUserProfilePage($accountController);
$profileView->handleRequest();
?>
