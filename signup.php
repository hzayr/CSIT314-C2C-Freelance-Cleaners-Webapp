<?php
session_start();
include 'connectDatabase.php';

// ENTITY LAYER: Represents user data and database operations
class UserAccount {
    private $db;
    public $username;
    public $password;
    public $email;
    public $phone_num;
    public $role;
    public $first_name;
    public $last_name;
    public $about;
    public $gender;
    public $profile_image;

    public function __construct($db, $data) {
        $this->db = $db;
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone_num = $data['phone_num'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->about = $data['about'] ?? '';
        $this->gender = $data['gender'] ?? '';
        $this->profile_image = $data['profile_image'] ?? null;
    }

    /**
     * Creates a new user account and profile
     * @return bool True if successful, false otherwise
     */
    public function createUserAccount() {
        $roleMapping = [
            'user admin' => 1,
            'cleaner' => 2,
            'home owner' => 3,
            'platform management' => 4
        ];

        if (!array_key_exists($this->role, $roleMapping)) {
            return false;
        }

        $role_id = $roleMapping[$this->role];
        
        // Start transaction
        $this->db->begin_transaction();

        try {
            // Insert into users table
            $stmt = $this->db->prepare("INSERT INTO users (username, password, role_id, email, phone_num, status_id) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssiss", $this->username, $this->password, $role_id, $this->email, $this->phone_num);
            $stmt->execute();
            $user_id = $this->db->insert_id;

            // Insert into profile table
            $stmt = $this->db->prepare("INSERT INTO profile (user_id, first_name, last_name, about, gender, profile_image, status_id) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("issssb", $user_id, $this->first_name, $this->last_name, $this->about, $this->gender, $this->profile_image);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Checks if username already exists
     * @return bool True if username exists, false otherwise
     */
    public function isUsernameExists() {
        $stmt = $this->db->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}

// CONTROL LAYER: Manages registration logic
class RegistrationController {
    private $userAccount;

    public function __construct($userAccount) {
        $this->userAccount = $userAccount;
    }

    /**
     * Validates and processes user registration
     * @return array Result of registration attempt
     */
    public function registerUser() {
        if ($this->userAccount->isUsernameExists()) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        if ($this->userAccount->createUserAccount()) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
}

// BOUNDARY LAYER: Handles user interface and form processing
class RegistrationPage {
    /**
     * Processes registration form submission
     */
    public static function handleRegistration() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = (new Database())->getConnection();
            
            // Handle file upload
            $profile_image = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $profile_image = file_get_contents($_FILES['profile_image']['tmp_name']);
            }

            $userData = [
                'username' => htmlspecialchars($_POST['username'] ?? ''),
                'password' => htmlspecialchars($_POST['password'] ?? ''),
                'email' => htmlspecialchars($_POST['email'] ?? ''),
                'phone_num' => htmlspecialchars($_POST['phone_num'] ?? ''),
                'role' => htmlspecialchars($_POST['role'] ?? ''),
                'first_name' => htmlspecialchars($_POST['first_name'] ?? ''),
                'last_name' => htmlspecialchars($_POST['last_name'] ?? ''),
                'about' => htmlspecialchars($_POST['about'] ?? ''),
                'gender' => htmlspecialchars($_POST['gender'] ?? ''),
                'profile_image' => $profile_image
            ];

            $userAccount = new UserAccount($db, $userData);
            $controller = new RegistrationController($userAccount);
            $result = $controller->registerUser();

            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                self::RegistrationUI($userData);
            }
        } else {
            self::RegistrationUI();
        }
    }

    /**
     * Renders the registration form UI
     * @param array|null $formData Pre-filled form data
     */
    public static function RegistrationUI($formData = null) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sign Up - clean.sg</title>
            <link rel="stylesheet" href="./style.css">
            <link rel="stylesheet" href="./fontawesome-free-6.4.0-web/css/all.css">
            <style>
                body {
                    min-height: 100vh;
                    overflow-y: auto;
                }
                .login-section {
                    min-height: 100vh;
                    padding: 40px 0;
                }
                .login-container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
            </style>
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
                    <h2>Create Your Account</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                        <p class="error-message"><?php echo $_SESSION['error']; ?></p>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" class="login-form" enctype="multipart/form-data" style="margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                                   placeholder="Choose a username">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Create a password">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                   placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label for="phone_num">Phone Number</label>
                            <input type="tel" id="phone_num" name="phone_num" required 
                                   value="<?php echo htmlspecialchars($formData['phone_num'] ?? ''); ?>"
                                   placeholder="Enter your phone number">
                        </div>

                        <div class="form-group">
                            <label for="role">Register As</label>
                            <select id="role" name="role" required>
                                <option value="">Select a role</option>
                                <option value="user admin" <?php echo ($formData['role'] ?? '') == 'user admin' ? 'selected' : ''; ?>>User Admin</option>
                                <option value="cleaner" <?php echo ($formData['role'] ?? '') == 'cleaner' ? 'selected' : ''; ?>>Cleaner</option>
                                <option value="home owner" <?php echo ($formData['role'] ?? '') == 'home owner' ? 'selected' : ''; ?>>Home Owner</option>
                                <option value="platform management" <?php echo ($formData['role'] ?? '') == 'platform management' ? 'selected' : ''; ?>>Platform Management</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>"
                                   placeholder="Enter your first name">
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>"
                                   placeholder="Enter your last name">
                        </div>

                        <div class="form-group">
                            <label for="about">About</label>
                            <textarea id="about" name="about" 
                                      placeholder="Tell us about yourself"><?php echo htmlspecialchars($formData['about'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male" <?php echo ($formData['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($formData['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($formData['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="profile_image">Profile Image</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                        </div>

                        <button type="submit" class="login-btn">Sign Up</button>

                        <p style="text-align: center; margin-top: 1.5rem; color: var(--color-gray);">
                            Already have an account? <a href="login.php" style="color: var(--color-primary); font-weight: 500;">Sign in</a>
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
RegistrationPage::handleRegistration();
?> 