<?php
require '../connectDatabase.php';
session_start();

// BOUNDARY LAYER: HTML View for updating user profiles
class UpdateUserProfilePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        if (!$this->controller->isUserLoggedIn()) {
            header("Location: login.php");
            exit();
        }

        // Get user data if user_id is provided
        $userData = null;
        if (isset($_GET['user_id'])) {
            $userData = $this->controller->getUserData($_GET['user_id']);
            if (!$userData) {
                header("Location: admin_manage_user_profiles.php");
                exit();
            }
        }

        $success = false;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update'])) {
                $updatedData = $this->gatherUserData();
                $result = $this->controller->updateUserProfile($updatedData);
                
                if ($result['success']) {
                    $success = true;
                    $userData = $this->controller->getUserData($updatedData['user_id']); // Refresh user data
                } else {
                    $error = $result['message'];
                }
            }
        }

        $roles = $this->controller->getRoles();
        $this->UpdateUserProfileUI($roles, $userData, $error, $success);
    }

    private function gatherUserData() {
        return [
            'user_id' => $_POST['user_id'] ?? '',
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

    public function UpdateUserProfileUI($roles, $userData, $error = null, $success = false) {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update User Profile</title>
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
                .update-button {
                    background-color: #238636;
                    color: white;
                    padding: 12px 24px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s;
                }
                .update-button:hover {
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
                .success-message {
                    color: #238636;
                    text-align: center;
                    margin-bottom: 20px;
                    background-color: #21262d;
                    padding: 10px;
                    border-radius: 6px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <h1>Update User Profile</h1>
            
            <div class="form-container">
                <?php if ($success): ?>
                    <div class="success-message">Profile updated successfully!</div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($userData): ?>
                    <div class="role-display">
                        Updating profile for: <?php echo htmlspecialchars($userData['username']); ?>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userData['user_id']); ?>">
                        
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($userData['password']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_num">Phone Number:</label>
                            <input type="text" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($userData['phone_num']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="about">About:</label>
                            <input type="text" id="about" name="about" value="<?php echo htmlspecialchars($userData['about']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male" <?php echo $userData['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $userData['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $userData['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="role_id">Role:</label>
                            <select id="role_id" name="role_id" required>
                                <option value="">Select a role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo htmlspecialchars($role['role_id']); ?>" 
                                            <?php echo $userData['role_id'] == $role['role_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['role_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="button-group">
                            <button type="submit" name="update" class="update-button">Update Profile</button>
                            <a href="admin_update_profile.php?role_id=<?php echo htmlspecialchars($userData['role_id']); ?>" class="return-button">Return</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="error-message">No user data found.</div>
                    <div class="button-group">
                        <a href="admin_update_profile.php" class="return-button">Return</a>
                    </div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
}

// CONTROL LAYER: Manages data flow between boundary and entity layers
class UpdateUserProfileController {
    private $userProfile;

    public function __construct($userProfile) {
        $this->userProfile = $userProfile;
    }

    public function isUserLoggedIn() {
        return isset($_SESSION['username']);
    }

    public function getUserData($userId) {
        return $this->userProfile->getUserData($userId);
    }

    public function updateUserProfile($userData) {
        // Validate input data
        $validationResult = $this->validateUserData($userData);
        if (!$validationResult['isValid']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        // Attempt to update user profile
        $result = $this->userProfile->updateUserProfile(
            $userData['user_id'],
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

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Failed to update profile. Please check if the user exists and try again.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Profile updated successfully'
        ];
    }

    private function validateUserData($userData) {
        $requiredFields = ['user_id', 'username', 'password', 'email', 'phone_num', 'role_id', 'first_name', 'last_name', 'about', 'gender'];
        
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

    public function getUserData($userId) {
        try {
            $query = "SELECT u.*, p.first_name, p.last_name, p.about, p.gender, p.profile_id 
                     FROM users u 
                     LEFT JOIN profile p ON u.user_id = p.user_id 
                     WHERE u.user_id = ?";
            $stmt = $this->mysqli->prepare($query);
            if (!$stmt) {
                error_log("Failed to prepare getUserData statement: " . $this->mysqli->error);
                return null;
            }
            
            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) {
                error_log("Failed to execute getUserData query: " . $stmt->error);
                return null;
            }
            
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();
            $stmt->close();
            
            if (!$userData) {
                error_log("No user data found for user_id: " . $userId);
                return null;
            }
            
            return $userData;
        } catch (Exception $e) {
            error_log("Error in getUserData: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserProfile($userId, $username, $password, $email, $phone_num, $role_id, $first_name, $last_name, $about, $gender) {
        $this->mysqli->begin_transaction();
        try {
            // Update users table
            $query = "UPDATE users SET username = ?, password = ?, email = ?, phone_num = ?, role_id = ? WHERE user_id = ?";
            $stmt = $this->mysqli->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare users update statement: " . $this->mysqli->error);
            }
            $stmt->bind_param("ssssii", $username, $password, $email, $phone_num, $role_id, $userId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute users update: " . $stmt->error);
            }
            $stmt->close();

            // Check if profile exists
            $checkQuery = "SELECT profile_id FROM profile WHERE user_id = ?";
            $checkStmt = $this->mysqli->prepare($checkQuery);
            if (!$checkStmt) {
                throw new Exception("Failed to prepare profile check statement: " . $this->mysqli->error);
            }
            $checkStmt->bind_param("i", $userId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $profileExists = $result->num_rows > 0;
            $checkStmt->close();

            if ($profileExists) {
                // Update existing profile
                $query2 = "UPDATE profile SET first_name = ?, last_name = ?, about = ?, gender = ? WHERE user_id = ?";
                $stmt2 = $this->mysqli->prepare($query2);
                if (!$stmt2) {
                    throw new Exception("Failed to prepare profile update statement: " . $this->mysqli->error);
                }
                $stmt2->bind_param("ssssi", $first_name, $last_name, $about, $gender, $userId);
                if (!$stmt2->execute()) {
                    throw new Exception("Failed to execute profile update: " . $stmt2->error);
                }
                $stmt2->close();
            } else {
                // Insert new profile
                $query2 = "INSERT INTO profile (user_id, first_name, last_name, about, gender, status_id) VALUES (?, ?, ?, ?, ?, 1)";
                $stmt2 = $this->mysqli->prepare($query2);
                if (!$stmt2) {
                    throw new Exception("Failed to prepare profile insert statement: " . $this->mysqli->error);
                }
                $stmt2->bind_param("issss", $userId, $first_name, $last_name, $about, $gender);
                if (!$stmt2->execute()) {
                    throw new Exception("Failed to execute profile insert: " . $stmt2->error);
                }
                $stmt2->close();
            }

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Profile update error: " . $e->getMessage());
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
$userController = new UpdateUserProfileController($userProfileEntity);
$userProfileView = new UpdateUserProfilePage($userController);
$userProfileView->handleRequest();

$database->closeConnection();
?>
