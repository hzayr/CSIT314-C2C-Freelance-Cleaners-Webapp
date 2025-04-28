<?php
require '../connectDatabase.php';
if (!$conn) {
    echo "Database connection error: " . mysqli_connect_error();
}

$userprofile_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : null;

if ($userprofile_id === null) {
    echo "Error: userprofile_id is null.";
    exit();
}


// Entity class: Handles database operations and acts as the data structure for UserProfile
class UserProfile {
    private $conn;
    private $userprofile_id;
    private $userprofile_description;

    public function __construct($userprofile_id = null) {
        $this->conn = $this->getConnection();
        if ($userprofile_id) {
            $this->userprofile_id = $userprofile_id;
            $this->loadUserProfile();
        }
    }

    private function getConnection() {
        global $conn;
        return $conn;
    }

    public function getUserProfileId() {
        return $this->userprofile_id;
    }

    public function getUserProfileDescription() {
        return $this->userprofile_description;
    }

    public function setUserProfileDescription($userprofile_description) {
        $this->userprofile_description = $userprofile_description;
    }

    public function loadUserProfile() {
        $stmt = $this->conn->prepare("SELECT * FROM role WHERE role_id = ?");
        $stmt->bind_param("i", $this->userprofile_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $this->userprofile_description = $row['role_description'];
        }
    }

    public function updateUserProfileDescription(UserProfile $userProfile) {
        $stmt = $this->conn->prepare("UPDATE role SET role_description = ? WHERE role_id = ?");
        $stmt->bind_param("si", $userProfile->userprofile_description, $userProfile->userprofile_id);

        // Debugging: Check if the statement prepares correctly
        if (!$stmt) {
            echo "Error preparing statement: " . $this->conn->error;
            return false;
        }

        if (!$stmt->execute()) {
            echo "SQL execution error: " . $stmt->error;
            return false;
        }


        $executeResult = $stmt->execute();

        // Debugging: Check if the statement executes correctly
        if (!$executeResult) {
            echo "Error executing statement: " . $stmt->error;
            return false;
        }

        return $executeResult;
    }
}

// Controller class: Calls methods in the UserProfile entity
class UpdateUserProfileDescriptionController {
    private $profile;

    public function __construct($userprofile_id) {
        $this->profile = new UserProfile($userprofile_id);
    }

    public function getUserProfile() {
        return $this->profile;
    }

    public function updateUserProfileDescription(UserProfile $userProfile) {
        return $userProfile->updateUserProfileDescription($userProfile);
    }
}

// Boundary class: Handles display and form interactions
class UpdateUserProfileDescriptionPage {
    private $profileController;

    public function __construct($profileController) {
        $this->profileController = $profileController;
    }

    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_description'])) {
            $new_description = trim($_POST['role_description']);
            $profile = $this->profileController->getUserProfile();
            $profile->setUserProfileDescription($new_description);

            if ($this->profileController->updateUserProfileDescription($profile)) {
                echo "<p class='success-message'>User Profile description updated successfully.</p>";
            } else {
                echo "<p class='error-message'>Error updating description.</p>";
            }
        }
    }

    public function UpdateUserProfileDescriptionUI() {
        $profile = $this->profileController->getUserProfile();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Update User Profile Description</title>
            <style>
                body {
                    font-family: 'Poppins', 'Arial', sans-serif;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    margin: 0;
                    padding: 20px;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }

                h1 {
                    color: #58a6ff;
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 24px;
                    font-weight: 600;
                }

                .form-container {
                    background: #161b22;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                    max-width: 600px;
                    margin: auto;
                    width: 100%;
                    box-sizing: border-box;
                }

                .button-container {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    width: 100%;
                }

                label {
                    display: block;
                    margin-bottom: 10px;
                    font-weight: 500;
                    font-size: 16px;
                    color: #c9d1d9;
                }

                textarea {
                    width: 100%;
                    height: 120px;
                    margin-bottom: 20px;
                    padding: 12px;
                    border: 1px solid #30363d;
                    border-radius: 6px;
                    font-size: 16px;
                    resize: vertical;
                    background-color: #0d1117;
                    color: #c9d1d9;
                }

                textarea:focus {
                    outline: none;
                    border-color: #58a6ff;
                }

                button, .return-button {
                    padding: 14px 20px;
                    border-radius: 6px;
                    font-size: 16px;
                    width: 100%;
                    transition: background-color 0.3s;
                    box-sizing: border-box;
                }

                button {
                    background-color: #238636;
                    color: white;
                    border: none;
                    cursor: pointer;
                }

                button:hover {
                    background-color: #2ea043;
                }

                .return-button {
                    margin-top: 20px;
                    display: inline-block;
                    background-color:hsl(0, 0.00%, 50%);
                    color: white;
                    text-decoration: none;
                    text-align: center;
                }

                .return-button:hover {
                    background-color:hsl(0, 0.00%, 50%);
                }

                .success-message {
                    color: #2ea043;
                    font-size: 16px;
                    text-align: center;
                    margin-top: 20px;
                }

                .error-message {
                    color: #f85149;
                    font-size: 16px;
                    text-align: center;
                    margin-top: 20px;
                }

                .form-container a {
                    display: inline-block;
                    margin-top: 10px;
                    text-align: center;
                    width: 100%;
                }

                @media (max-width: 768px) {
                    .form-container {
                        width: 95%;
                        padding: 20px;
                    }
                    h1 {
                        font-size: 1.8rem;
                    }
                }
            </style>
        </head>
        <body>
        <h1>Update User Profile Description</h1>
        <div class="form-container">
            <form action="" method="post">
                <label for="role_description">New Description:</label>
                <textarea name="role_description" id="role_description" required><?php echo htmlspecialchars($profile->getUserProfileDescription()); ?></textarea>
                <div class="button-container">
                    <button type="submit">Update Description</button>
                    <a href="admin_manage_user_profiles.php" class="return-button">Return</a>
                </div>
            </form>
        </div>
        </body>
        </html>
        <?php
    }
}

// Main script to initialize the Controller and Boundary
$profileController = new UpdateUserProfileDescriptionController($userprofile_id);
$userprofileBoundary = new UpdateUserProfileDescriptionPage($profileController);
$userprofileBoundary->handleFormSubmission();
$userprofileBoundary->UpdateUserProfileDescriptionUI();
?>
