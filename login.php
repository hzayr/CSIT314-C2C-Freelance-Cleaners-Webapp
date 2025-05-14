<?php
session_start();
include 'connectDatabase.php';

// ENTITY LAYER: Represents user data and database operations
class UserAccount {
    private $db;
    public $username;
    public $password;
    public $role;

    public function __construct($db, $username, $password, $role) {
        $this->db = $db;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Verifies user login credentials against the database
     * @param UserAccount $userAccount The user account to verify
     * @return array|false Returns user data if credentials are valid, false otherwise
     */
    public function verifyLoginCredentials($userAccount) {
        $roleMapping = [
            'user admin' => 1,
            'cleaner' => 2,
            'home owner' => 3,
            'platform management' => 4
        ];

        if (!array_key_exists($userAccount->role, $roleMapping)) {
            return false;
        }

        $role_id = $roleMapping[$userAccount->role];
        $stmt = $this->db->prepare("SELECT user_id, password, status_id FROM users WHERE username = ? AND role_id = ?");
        $stmt->bind_param("si", $userAccount->username, $role_id);

        if (!$stmt->execute()) {
            return false;
        }

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $stored_password, $status_id);
            $stmt->fetch();
            return ['user_id' => $user_id, 'password' => $stored_password, 'status_id' => $status_id];
        } else {
            return false;
        }
    }
}

// CONTROL LAYER: Manages login logic and session handling
class LoginController {
    private $isSuspended = false;

    /**
     * Verifies login credentials and handles account suspension
     * @param UserAccount $userAccount The user account to verify
     * @return bool True if login is successful, false otherwise
     */
    public function verifyLoginCredentials($userAccount) {
        $userData = $userAccount->verifyLoginCredentials($userAccount);

        if ($userData === false) {
            return false;
        }

        if ($userData['status_id'] == 2) {
            $this->isSuspended = true;
            return false;
        }

        return $userData['password'] === $userAccount->password;
    }

    /**
     * Checks if the account is suspended
     * @return bool True if account is suspended, false otherwise
     */
    public function isSuspended() {
        return $this->isSuspended;
    }

    /**
     * Retrieves the user ID for the given account
     * @param UserAccount $userAccount The user account
     * @return int The user ID
     */
    public function getUserId($userAccount) {
        $userData = $userAccount->verifyLoginCredentials($userAccount);
        return $userData['user_id'];
    }
}

// BOUNDARY LAYER: Handles user interface and form processing
class LoginPage {
    /**
     * Processes login form submission and handles user authentication
     */
    public static function handleLogin() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = htmlspecialchars($_POST['username'] ?? '');
            $password = htmlspecialchars($_POST['password'] ?? '');
            $role = htmlspecialchars($_POST['role'] ?? '');

            if ($username && $password && $role) {
                $db = (new Database())->getConnection();
                $user = new UserAccount($db, $username, $password, $role);

                $authController = new LoginController();
                $authResult = $authController->verifyLoginCredentials($user);

                if ($authResult === true) {
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $authController->getUserId($user);
                    self::redirectToDashboard($role);
                    exit();
                } else {
                    $_SESSION['message'] = $authController->isSuspended()
                        ? "Account suspended. Please contact support."
                        : "Invalid username, password, or role.";
                    header("Location: login.php?role=" . $role);
                    exit();
                }
            } else {
                self::LoginUI($role);
            }
        } else {
            $role = $_GET['role'] ?? '';
            self::LoginUI($role);
        }
    }

    /**
     * Redirects user to appropriate dashboard based on role
     * @param string $role The user's role
     */
    public static function redirectToDashboard($role) {
        switch($role) {
            case 'user admin':
                header("Location: User Admin/admin_dashboard.php");
                break;
            case 'cleaner':
                header("Location: cleaner/cleaner_dashboard.php");
                break;
            case 'home owner':
                header("Location: home owner/homeowner_dashboard.php");
                break;
            case 'platform management':
                header("Location: platform management/pm_dashboard.php");
                break;
        }
        exit();
    }

    /**
     * Renders the login form UI
     * @param string|null $selectedRole The pre-selected role in the form
     */
    public static function LoginUI($selectedRole = null) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - clean.sg</title>
            <link rel="stylesheet" href="./style.css">
            <link rel="stylesheet" href="./fontawesome-free-6.4.0-web/css/all.css">
        </head>
        <body>
            <nav>
                <div class="container nav-container">
                    <a href="index.html" class="logo"><h3>clean.sg</h3></a>
                    <ul class="social-link">
                        <li><a href="https://t.me/+WvqfOz0QNlA0ZjI1" target="_blank"><i class="fab fa-telegram"></i></a></li>
                    </ul>
                </div>
            </nav>

            <div class="login-section">
                <div class="login-container">
                    <h2>
                        <?php
                            if ($selectedRole) {
                                if ($selectedRole == 'seller') {
                                    echo 'Start Selling Today!';
                                } elseif ($selectedRole == 'cleaner') {
                                    echo 'Start Publicizing Today!';
                                } elseif ($selectedRole == 'user admin') {
                                    echo 'User Admin';
                                } elseif ($selectedRole == 'home owner') {
                                    echo 'Welcome Back';
                                }
                            } else {
                                echo 'Welcome Back';
                            }
                        ?>
                    </h2>

                    <?php if (isset($_SESSION['message'])): ?>
                        <p class="session-message"><?php echo $_SESSION['message']; ?></p>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

                    <form method="POST" class="login-form">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required placeholder="Enter your username">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required placeholder="Enter your password">
                        </div>

                        <div class="form-group">
                            <label for="role">Login As</label>
                            <select id="role" name="role" required>
                                <option value="user admin" <?php echo ($selectedRole == 'user admin') ? 'selected' : ''; ?>>User Admin</option>
                                <option value="cleaner" <?php echo ($selectedRole == 'cleaner') ? 'selected' : ''; ?>>Cleaner</option>
                                <option value="home owner" <?php echo ($selectedRole == 'home owner') ? 'selected' : ''; ?>>Home Owner</option>
                                <option value="platform management" <?php echo ($selectedRole == 'platform management') ? 'selected' : ''; ?>>Platform Management</option>
                            </select>
                        </div>

                        <button type="submit" class="login-btn">Sign In</button>

                        <p style="text-align: center; margin-top: 1.5rem; color: var(--color-gray);">
                            Don't have an account? <a href="signup.php" style="color: var(--color-primary); font-weight: 500;">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main execution
LoginPage::handleLogin();
?>
