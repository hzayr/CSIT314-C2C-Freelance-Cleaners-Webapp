<?php
session_start();
require '../connectDatabase.php';

// ENTITY: Represents user data and database retrieval
class UserAccount {
    private $pdo;

    public function __construct() {
        $this->connectDatabase();
    }

    private function connectDatabase() {
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

    public function viewUserAccountByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT u.user_id, u.username, u.password, r.role_name, u.email, u.phone_num, 
            s.status_name, p.first_name, p.last_name, p.about, p.profile_image
            FROM users u
            JOIN role r ON u.role_id = r.role_id
            JOIN status s ON s.status_id = u.status_id
            LEFT JOIN profile p ON u.user_id = p.user_id
            WHERE u.username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// CONTROL LAYER: Manages data flow between boundary and entity layers
class ViewUserAccountController {
    private $userAccountModel;

    public function __construct($userAccountModel) {
        $this->userAccountModel = $userAccountModel;
    }

    public function viewUserAccount($username) {
        return $this->userAccountModel->viewUserAccountByUsername($username);
    }
}

// BOUNDARY LAYER: Handles user interactions and rendering user information
class ViewUserAccountPage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        // Check if user is logged in
        if (!isset($_SESSION['username'])) {
            header("Location: ../login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        } else {
            $username = $_GET['username'] ?? '';
            if ($username) {
                $accountData = $this->controller->viewUserAccount($username);
                $this->ViewUserAccountUI($accountData);
            }
        }
    }

    private function handlePostRequest() {
        if (isset($_POST['action'])) {
            $username = $_POST['username'];
            switch ($_POST['action']) {
                case 'return':
                    header("Location: admin_manage_user_acc.php");
                    exit();
                case 'update':
                    header("Location: admin_update_user_acc.php?username=" . urlencode($username));
                    exit();
                case 'suspend':
                    header("Location: admin_suspend_user_acc.php?username=" . urlencode($username));
                    exit();
            }
        }
    }

    public function ViewUserAccountUI($accountData) {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Account Information</title>
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
                    font-size: 36px;
                    color: #58a6ff;
                    text-align: center;
                    margin-top: 20px;
                    font-weight: 600;
                }

                .container {
                    width: 90%;
                    max-width: 1000px;
                    margin: auto;
                    background-color: #161b22;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                }

                table {
                    width: 100%;
                    margin: 20px auto;
                    border-collapse: collapse;
                }

                th, td {
                    padding: 14px 18px;
                    text-align: center;
                    font-size: 16px;
                    border-bottom: 1px solid #21262d;
                }

                th {
                    background-color: #21262d;
                    color: #58a6ff;
                    font-weight: 600;
                    font-size: 1rem;
                }

                tr:hover {
                    background-color: #30363d;
                }

                .button-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 15px;
                    margin-top: 20px;
                }

                .action-buttons {
                    display: flex;
                    gap: 10px;
                }

                .action-button {
                    background-color: #238636;
                    color: white;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                    transition: background 0.3s;
                }

                .action-button:hover {
                    background-color: #2ea043;
                }

                .return-button {
                    background-color: #6e7681;
                    color: white;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                    transition: background 0.3s;
                }

                .return-button:hover {
                    background-color: #8b949e;
                }

                .action-button.suspend {
                    background-color: #da3633;
                }

                .action-button.suspend:hover {
                    background-color: #f85149;
                }

                img {
                    max-width: 200px;
                    max-height: 200px;
                    border-radius: 50%;
                }

                .profile-info {
                    margin-top: 10px;
                    font-size: 20px;
                    color: #c9d1d9;
                }

                .profile-info strong {
                    font-weight: 600;
                }

                @media (max-width: 768px) {
                    .container {
                        width: 95%;
                    }
                    h1 {
                        font-size: 1.8rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Account Information</h1>
                <table>
                    <tr>
                        <td><strong>Profile Image</strong></td>
                        <td colspan="2">
                            <?php
                            if (!empty($accountData['profile_image'])) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($accountData['profile_image']) . '" alt="Profile Image" />';
                            } else {
                                echo 'No profile image available.';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Full Name</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['first_name'] . ' ' . htmlspecialchars($accountData['last_name'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>About</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['about'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['username'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Password</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['password'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['role_name'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['email'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone Number</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['phone_num'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($accountData['status_name']); ?></td>
                    </tr>
                </table>

                <div class="button-container">
                    <div class="action-buttons">
                        <form action="" method="post">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($accountData['username']); ?>">
                            <button type="submit" name="action" value="update" class="action-button">Update account information</button>
                        </form>
                        <form action="" method="post">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($accountData['username']); ?>">
                            <button type="submit" name="action" value="suspend" class="action-button suspend">Suspend account</button>
                        </form>
                    </div>
                    <form action="" method="post">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($accountData['username']); ?>">
                        <button type="submit" name="action" value="return" class="return-button">Return</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main logic: Initialization and request handling
$userAccountEntity = new UserAccount();
$controller = new ViewUserAccountController($userAccountEntity);
$view = new ViewUserAccountPage($controller);
$view->handleRequest();
?>
