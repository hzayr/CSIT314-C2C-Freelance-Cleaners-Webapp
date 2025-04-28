<?php
session_start();

// BOUNDARY LAYER: Responsible for rendering user information and handling requests
class SuspendUserAccountPage {
    private $accountController;
    private $accountData;

    public function __construct() {
        $this->accountController = new SuspendUserAccountController();
    }

    public function handleRequest() {
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $username = $_POST['username'];
            $action = $_POST['action'];

            if ($action === 'suspend') {
                $success = $this->accountController->suspendUserAccount($username);
                $_SESSION['message'] = $success ? "User account suspended successfully." : "Failed to suspend user account.";
            } elseif ($action === 'Remove') {
                $success = $this->accountController->unsuspendUserAccount($username);
                $_SESSION['message'] = $success ? "User account suspension removed." : "Failed to remove suspension.";
            }

            echo "<script>
                alert('" . htmlspecialchars($_SESSION['message']) . "');
                window.location.href = 'admin_manage_user_acc.php';
            </script>";
            exit();
        }

        $username = isset($_GET['username']) ? $_GET['username'] : '';
        if ($username) {
            $this->accountData = $this->accountController->getUserAccount($username);
        } else {
            echo "No username provided.";
        }
    }

    private function getUsername() {
        return $this->accountData['username'] ?? '';
    }

    private function getPassword() {
        return $this->accountData['password'] ?? '';
    }

    private function getRoleName() {
        return $this->accountData['role_name'] ?? '';
    }

    private function getEmail() {
        return $this->accountData['email'] ?? '';
    }

    private function getPhoneNumber() {
        return $this->accountData['phone_num'] ?? '';
    }

    private function getStatusName() {
        return $this->accountData['status_name'] ?? '';
    }

    public function SuspendUserAccountUI() {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Suspend Confirmation</title>
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
        </head>
        <body>
            <h1>Suspend this account?</h1>
            <table id="infoTable">
                <tr>
                    <th>Username</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getUsername()); ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getPassword()); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getRoleName()); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getEmail()); ?></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getPhoneNumber()); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td colspan="2"><?php echo htmlspecialchars($this->getStatusName()); ?></td>
                </tr>
            </table>
            <div class="button-container">
                <?php if ($this->getStatusName() === 'Active'): ?>
                    <form action="" method="POST" class="form-body"> 
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($this->getUsername()); ?>">
                        <input type="hidden" name="action" value="suspend">
                        <button type="submit" class="suspend-btn">Suspend</button>
                    </form>
                <?php elseif ($this->getStatusName() === 'Suspended'): ?>
                    <form action="" method="POST" class="form-body"> 
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($this->getUsername()); ?>">
                        <input type="hidden" name="action" value="Remove">
                        <button type="submit" class="remove-suspend-btn">Remove Suspension</button>
                    </form>
                <?php endif; ?>
                <form action="admin_manage_user_acc.php" class="form-body">
                    <button type="submit" class="return-btn">Return</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}


// CONTROL LAYER: Serves as an intermediary between view and entity
class SuspendUserAccountController {
    private $userAccountModel;

    public function __construct() {
        $this->userAccountModel = new UserAccount();
    }

    public function getUserAccount($username) {
        return $this->userAccountModel->getUserAccountByUsername($username); //Returns the user account
    }

    public function suspendUserAccount($username) {
        return $this->userAccountModel->suspendUserAccount($username); //Returns true/false
    }

    public function unsuspendUserAccount($username) {
        return $this->userAccountModel->unsuspendUserAccount($username); //Returns true/false
    }
}


// ENTITY: Handles all logic for user data and database interactions
class UserAccount {
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

    public function getUserAccountByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT u.username, u.password, r.role_name, u.email, u.phone_num, s.status_name
            FROM users u
            JOIN role r ON u.role_id = r.role_id
            JOIN status s ON s.status_id = u.status_id
            WHERE u.username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function suspendUserAccount($username) {
        $stmt = $this->pdo->prepare("UPDATE users SET status_id = 2 WHERE username = :username");
        $stmt->bindParam(':username', $username);
        return $stmt->execute(); //return true after successfully updated database
    }

    public function unsuspendUserAccount($username) {
        $stmt = $this->pdo->prepare("UPDATE users SET status_id = 1 WHERE username = :username");
        $stmt->bindParam(':username', $username);
        return $stmt->execute(); //return true after successfully updated database
    }
}


// Now instantiate and handle the request in the Boundary layer
$page = new SuspendUserAccountPage();
$page->handleRequest();
$page->SuspendUserAccountUI();
?>
