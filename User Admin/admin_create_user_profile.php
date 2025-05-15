<?php
require '../connectDatabase.php';
session_start();

// BOUNDARY LAYER: HTML View for creating user accounts
class CreateUserProfilePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        if (!$this->controller->isUserLoggedIn()) {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create'])) {
                $userData = $this->gatherUserData();
                $result = $this->controller->createUserAccountWithProfile($userData);
                
                if ($result['success']) {
                    header("Location: admin_manage_user_profiles.php");
                    exit();
                } else {
                    $error = $result['message'];
                }
            }
        }

        $roles = $this->controller->getRoles();
        $this->CreateUserProfileUI($roles, $error ?? null);
    }

    private function gatherUserData() {
        return [
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone_num' => $_POST['phone_num'] ?? '',
            'role_id' => $_POST['role_id'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'about' => $_POST['about'] ?? '',
            'gender' => $_POST['gender'] ?? ''
        ];
    }

    public function CreateUserProfileUI($roles, $error = null) {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Create User Account</title>
            <style>
                body {
                    font-family: 'Poppins', 'Arial', sans-serif;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    margin: 0;
                    padding: 20px;
                    min-height: 100vh;
                }
                h1 {
                    text-align: center;
                    color: #58a6ff;
                    margin: 40px 0 20px 0;
                    font-weight: 600;
                }
                .form-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #161b22;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                }
                .form-group {
                    margin-bottom: 20px;
                }
                label {
                    display: block;
                    margin-bottom: 8px;
                    color: #8b949e;
                }
                input[type="text"],
                input[type="password"],
                input[type="email"],
                select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #30363d;
                    border-radius: 6px;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    font-size: 16px;
                }
                input[type="text"]:focus,
                input[type="password"]:focus,
                input[type="email"]:focus,
                select:focus {
                    outline: none;
                    border-color: #58a6ff;
                }
                .button-group {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                    margin-top: 30px;
                }
                .create-button {
                    background-color: #238636;
                    color: white;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s;
                }
                .create-button:hover {
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
                    transition: background-color 0.3s;
                }
                .return-button:hover {
                    background-color: #8b949e;
                }
                .error-message {
                    color: #f85149;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .role-display {
                    background-color: #21262d;
                    padding: 10px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    text-align: center;
                    color: #58a6ff;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <h1>Create User Account</h1>
            
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php
                // Get the role name for the selected role_id
                $selected_role = null;
                foreach ($roles as $role) {
                    if ($role['role_id'] == $_POST['role_id'] ?? null) {
                        $selected_role = $role;
                        break;
                    }
                }
                if ($selected_role): ?>
                    <div class="role-display">
                        Creating account for role: <?php echo htmlspecialchars($selected_role['role_name']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_num">Phone Number:</label>
                        <input type="text" id="phone_num" name="phone_num" required>
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>

                    <div class="form-group">
                        <label for="about">About:</label>
                        <input type="text" id="about" name="about" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role_id">Role:</label>
                        <select id="role_id" name="role_id" required>
                            <option value="">Select a role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="button-group">
                        <button type="submit" name="create" class="create-button">Create Account</button>
                        <a href="admin_manage_user_profiles.php" class="return-button">Return</a>
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// CONTROL LAYER: Manages data flow between boundary and entity layers
class CreateUserProfileController {
    private $userProfile;

    public function __construct($userProfile) {
        $this->userProfile = $userProfile;
    }

    public function isUserLoggedIn() {
        return isset($_SESSION['username']);
    }

    public function createUserAccountWithProfile($userData) {
        // Validate input data
        $validationResult = $this->validateUserData($userData);
        if (!$validationResult['isValid']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        // Attempt to create user account
        $result = $this->userProfile->createUserAccountWithProfile(
            $userData['username'],
            $userData['password'],
            $userData['email'],
            $userData['phone_num'],
            $userData['role_id'],
            $userData['first_name'],
            $userData['last_name'],
            $userData['about'],
            $userData['gender']
        );

        return [
            'success' => $result,
            'message' => $result ? 'User created successfully' : 'Failed to create user account or profile. Please try again.'
        ];
    }

    private function validateUserData($userData) {
        $requiredFields = ['username', 'password', 'email', 'phone_num', 'role_id', 'first_name', 'last_name', 'about', 'gender'];
        
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                return ['isValid' => false, 'message' => ucfirst($field) . ' is required.'];
            }
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['isValid' => false, 'message' => 'Invalid email format.'];
        }

        if (!preg_match('/^[0-9]{8,}$/', $userData['phone_num'])) {
            return ['isValid' => false, 'message' => 'Phone number must be at least 8 digits.'];
        }

        return ['isValid' => true];
    }

    public function getRoles() {
        return $this->userProfile->getAllRoles();
    }
}

// ENTITY LAYER: Handles database operations
class UserProfile {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function createUserAccountWithProfile($username, $password, $email, $phone_num, $role_id, $first_name, $last_name, $about, $gender) {
        $this->mysqli->begin_transaction();
        try {
            // Get the highest user_id and add 1
            $result = $this->mysqli->query("SELECT MAX(user_id) AS max_id FROM users");
            $row = $result->fetch_assoc();
            $new_user_id = ($row['max_id'] ?? 0) + 1;
            $status_id = 1;
            // Insert into users table with manual user_id
            $query = "INSERT INTO users (user_id, username, password, email, phone_num, role_id, status_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("issssii", $new_user_id, $username, $password, $email, $phone_num, $role_id, $status_id);
            $stmt->execute();
            if ($stmt->affected_rows === 0) throw new Exception('User insert failed');
            $user_id = $new_user_id;
            $stmt->close();
            // Insert into profile table
            $query2 = "INSERT INTO profile (user_id, first_name, last_name, about, gender, status_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt2 = $this->mysqli->prepare($query2);
            $stmt2->bind_param("issssi", $user_id, $first_name, $last_name, $about, $gender, $status_id);
            $stmt2->execute();
            if ($stmt2->affected_rows === 0) throw new Exception('Profile insert failed');
            $stmt2->close();
            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    public function getAllRoles() {
        $query = "SELECT role_id, role_name FROM role";
        $result = $this->mysqli->query($query);
        $roles = [];
    
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
    
        return $roles;
    }
}

// MAIN LOGIC: Initialize components and handle the request
$database = new Database();
$mysqli = $database->getConnection();

$userProfileEntity = new UserProfile($mysqli);
$userController = new CreateUserProfileController($userProfileEntity);
$userProfileView = new CreateUserProfilePage($userController);
$userProfileView->handleRequest();

$database->closeConnection();
?>
