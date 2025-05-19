<?php
require '../connectDatabase.php';
session_start();

// Entity Layer
class ProfileEntity {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getUserDetails($username) {
        $query = "SELECT u.*, p.first_name, p.last_name, p.about, p.gender, p.profile_id 
                 FROM users u
                 LEFT JOIN profile p ON u.user_id = p.user_id
                 WHERE u.username = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
        return $userData;
    }

    public function updateProfile($userData) {
        // Update users table
        $query = "UPDATE users SET email = ?, phone_num = ? WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $userData['email'], $userData['phone_num'], $userData['username']);
        $stmt->execute();
        $stmt->close();

        // Update profile table
        $query = "UPDATE profile SET first_name = ?, last_name = ?, about = ?, gender = ?, profile_image = ? 
                 WHERE user_id = (SELECT user_id FROM users WHERE username = ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssss", 
            $userData['first_name'], 
            $userData['last_name'], 
            $userData['about'], 
            $userData['gender'], 
            $userData['profile_image'], 
            $userData['username']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}

// Controller Layer
class ProfileController {
    private $profileEntity;

    public function __construct() {
        $this->profileEntity = new ProfileEntity();
    }

    public function getUserDetails($username) {
        return $this->profileEntity->getUserDetails($username);
    }

    public function handleProfileUpdate($userData) {
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'phone_num', 'gender'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                throw new Exception("Please fill in all required fields.");
            }
        }

        // Validate email format
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Validate phone number format (basic validation)
        if (!preg_match("/^[0-9]{8}$/", $userData['phone_num'])) {
            throw new Exception("Phone number must be 8 digits.");
        }

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
            $userData['profile_image'] = $imageData;
        } else {
            // If no new image uploaded, get existing image
            $existingUser = $this->getUserDetails($userData['username']);
            $userData['profile_image'] = $existingUser['profile_image'];
        }

        return $this->profileEntity->updateProfile($userData);
    }
}

// Boundary Layer
class ProfilePage {
    private $controller;
    private $message;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->message = "";
    }

    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userData = $_POST;
                // Add username from session
                $userData['username'] = $_SESSION['username'];
                
                if ($this->controller->handleProfileUpdate($userData)) {
                    $_SESSION['success_message'] = "Profile updated successfully.";
                    // Ensure no output before redirect
                    if (!headers_sent()) {
                        header("Location: cleaner_manage_profile.php");
                        exit();
                    } else {
                        echo '<script>window.location.href="cleaner_manage_profile.php";</script>';
                        exit();
                    }
                } else {
                    $this->message = "Error updating profile.";
                }
            } catch (Exception $e) {
                $this->message = $e->getMessage();
            }
        }
    }

    public function displayUpdateForm($username) {
        $userData = $this->controller->getUserDetails($username);
        
        if (!$userData) {
            echo "User not found.";
            return;
        }

        $this->handleFormSubmission();
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Update Profile</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                    background-color: #f4f4f4;
                }

                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }

                h2 {
                    text-align: center;
                    color: #333;
                    margin-bottom: 30px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #666;
                }

                input[type="text"],
                input[type="email"],
                input[type="tel"],
                textarea,
                select {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-sizing: border-box;
                }

                textarea {
                    height: 100px;
                    resize: vertical;
                }

                .error-message {
                    color: #dc3545;
                    margin-bottom: 20px;
                    text-align: center;
                }

                .button-group {
                    text-align: center;
                    margin-top: 20px;
                }

                .update-button {
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                }

                .update-button:hover {
                    background-color: #45a049;
                }

                .return-button {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #666;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }

                .return-button:hover {
                    background-color: #555;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Update Profile</h2>
                
                <?php if ($this->message): ?>
                    <div class="error-message"><?php echo htmlspecialchars($this->message); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_num">Phone Number:</label>
                        <input type="tel" id="phone_num" name="phone_num" 
                               value="<?php echo htmlspecialchars($userData['phone_num']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="M" <?php echo $userData['gender'] === 'M' ? 'selected' : ''; ?>>Male</option>
                            <option value="F" <?php echo $userData['gender'] === 'F' ? 'selected' : ''; ?>>Female</option>
                            <option value="O" <?php echo $userData['gender'] === 'O' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="about">About:</label>
                        <textarea id="about" name="about"><?php echo htmlspecialchars($userData['about']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="profile_image">Profile Image:</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    </div>

                    <div class="button-group">
                        <button type="submit" class="update-button">Update Profile</button>
                        <a href="cleaner_manage_profile.php" class="return-button">Return to Profile</a>
                    </div>
                </form>
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

$controller = new ProfileController();
$page = new ProfilePage($controller);
$page->displayUpdateForm($_SESSION['username']);
?>
