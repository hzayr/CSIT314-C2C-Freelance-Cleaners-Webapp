<?php
require "../connectDatabase.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Entity Layer
class ProfileEntity {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getProfileByUsername($username) {
        $query = "SELECT u.username, p.first_name, p.last_name, p.about, p.gender, 
                        u.email, p.user_id, r.role_name, u.phone_num, p.profile_image, p.profile_id 
                 FROM profile p 
                 JOIN users u ON p.user_id = u.user_id 
                 JOIN role r ON r.role_id = u.role_id 
                 WHERE u.username = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
}

// Controller Layer
class ProfileController {
    private $profileEntity;

    public function __construct() {
        $this->profileEntity = new ProfileEntity();
    }

    public function getProfile($username) {
        return $this->profileEntity->getProfileByUsername($username);
    }

    public function handleProfileAction($profile_id, $username) {
        if (isset($_POST['profile_id']) && isset($_POST['username'])) {
            header("Location: cleaner_update_profile.php?profile_id=" . urlencode($profile_id) . "&username=" . urlencode($username));
            exit();
        }
    }
}

// Boundary Layer
class ProfilePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function displayProfile($username) {
        $profile = $this->controller->getProfile($username);
        
        if (!$profile) {
            echo "Profile not found.";
            return;
        }

        $this->controller->handleProfileAction($profile['profile_id'], $username);
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Account Information</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                    color: #343a40;
                }

                h1 {
                    text-align: center;
                    color: #343a40;
                    margin-top: 20px;
                    font-size: 2em;
                }

                #infoTable {
                    width: 80%;
                    margin: 40px auto;
                    border-collapse: collapse;
                    background-color: #ffffff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                #infoTable th,
                #infoTable td {
                    padding: 15px;
                    text-align: left;
                    font-size: 18px;
                    border: 1px solid #dee2e6;
                }

                #infoTable th {
                    background-color: #6c757d;
                    color: #ffffff;
                    font-weight: bold;
                }

                #infoTable td:first-child {
                    font-weight: bold;
                    width: 30%;
                    text-align: right;
                }

                #infoTable td:nth-child(2) {
                    text-align: left;
                }

                #infoTable tr:nth-child(even) {
                    background-color: #f1f1f1;
                }

                .profile-image {
                    width: 150px;
                    height: 150px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid #6c757d;
                    margin: 15px 0;
                }

                .button-container {
                    text-align: center;
                    margin-top: 20px;
                }

                .button {
                    font-size: 18px;
                    padding: 10px 20px;
                    margin: 5px;
                    border: none;
                    border-radius: 5px;
                    color: #ffffff;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                    display: inline-block;
                }

                .button:not(.dashboard-button) {
                    background-color: #007bff;
                }

                .button:not(.dashboard-button):hover {
                    background-color: #0056b3;
                }

                .dashboard-button {
                    background-color: #6c757d;
                }

                .dashboard-button:hover {
                    background-color: #5a6268;
                }
            </style>
        </head>
        <body>
            <h1>Account Information</h1>
            <table id="infoTable">
                <?php if ($profile): ?>
                    <tr>
                        <td><strong>Account Picture</strong></td>
                        <td colspan="2">
                            <?php if (!empty($profile['profile_image'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($profile['profile_image']); ?>" class="profile-image" alt="Profile Picture">
                            <?php else: ?>
                                <img src="../default-profile.jpg" class="profile-image" alt="Default Profile Picture">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Full Name</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['role_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone Number</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['phone_num']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gender</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['gender']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>About</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($profile['about']); ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Profile not found.</td>
                    </tr>
                <?php endif; ?>
            </table>
            <div class="button-container">
                <form action="cleaner_dashboard.php" style="display: inline-block;">
                    <button type="submit" class="button dashboard-button">Return to main dashboard</button>
                </form>
                <form action="" method="POST" style="display: inline-block;">
                    <input type="hidden" name="profile_id" value="<?php echo htmlspecialchars($profile['profile_id']); ?>">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>">
                    <button type="submit" name="update" class="button">Update account profile</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$controller = new ProfileController();
$page = new ProfilePage($controller);
$page->displayProfile($_SESSION['username']);
?>
