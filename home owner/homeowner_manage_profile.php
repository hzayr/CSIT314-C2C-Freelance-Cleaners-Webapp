<?php
require "../connectDatabase.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Redirect to the update profile page if the update action is requested
if (isset($_POST['profile_id']) && isset($_POST['username'])) {
    $profile_id = $_POST['profile_id'];
    $username = $_POST['username'];
    header("Location: homeowner_update_profile.php?profile_id=" . urlencode($profile_id) . "&username=" . urlencode($username));
    exit();
}

$username = $_SESSION['username']; // Use the username from session

// ENTITY LAYER: Represents and fetches user profile data from the database
class UserAccount {
    public $username;
    public $first_name;
    public $last_name;
    public $about;
    public $gender;
    public $email;
    public $user_id;
    public $role_name;
    public $phone_num;
    public $profile_image;
    public $profile_id;

    public function __construct($data) {
        $this->username = $data['username'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->about = $data['about'];
        $this->gender = $data['gender'];
        $this->email = $data['email'];
        $this->user_id = $data['user_id'];
        $this->role_name = $data['role_name'];
        $this->phone_num = $data['phone_num'];
        $this->profile_image = $data['profile_image'];
        $this->profile_id = $data['profile_id'];
    }

    // Fetches profile data directly from the database
    public static function getProfileByUsername($username) {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1"; // using 127.0.0.1 to avoid socket issues
            $dbUsername = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') ?: "";
            $dbname = getenv('DB_NAME') ?: "csit314";
            $port = getenv('DB_PORT') ?: 3307;
    
            $dsn = "mysql:host={$servername};port={$port};dbname={$dbname}";
            $pdo = new PDO($dsn, $dbUsername, $password);
    
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $query = "SELECT u.username, p.first_name, p.last_name, p.about, p.gender, u.email, p.user_id, r.role_name, u.phone_num, p.profile_image, p.profile_id 
                      FROM profile p 
                      JOIN users u ON p.user_id = u.user_id 
                      JOIN role r ON r.role_id = u.role_id 
                      WHERE u.username = :username";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new self($data) : null;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

// CONTROL LAYER: Handles business logic and manages the entity layer
class ViewBuyerAccountController {
    // Fetches the profile as a UserAccount object
    public function getProfile($username) {
        return UserAccount::getProfileByUsername($username);
    }
}

// BOUNDARY LAYER: Responsible for rendering the user interface
class ViewBuyerAccountPage {
    private $profileData;

    public function __construct($profileData) {
        $this->profileData = $profileData;
    }

    // Renders the profile page
    public function render() {
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

                /* Center the button container */
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

                /* Update profile button (default blue) */
                .button:not(.dashboard-button) {
                    background-color: #007bff;
                }

                .button:not(.dashboard-button):hover {
                    background-color: #0056b3;
                }

                /* Dashboard button styling */
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
                <?php if ($this->profileData): ?>
                    <tr>
                        <td><strong>Account Picture</strong></td>
                        <td colspan="2">
                            <?php if (!empty($this->profileData->profile_image)): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($this->profileData->profile_image); ?>" class="profile-image" alt="Profile Picture">
                            <?php else: ?>
                                <img src="../default-profile.jpg" class="profile-image" alt="Default Profile Picture">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Full Name</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->first_name . ' ' . $this->profileData->last_name); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->role_name); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->email); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone Number</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->phone_num); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gender</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->gender); ?></td>
                    </tr>
                    <tr>
                        <td><strong>About</strong></td>
                        <td colspan="2"><?php echo htmlspecialchars($this->profileData->about); ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Profile not found.</td>
                    </tr>
                <?php endif; ?>
            </table>
            <div class="button-container">
                <form action="homeowner_dashboard.php" style="display: inline-block;">
                    <button type="submit" class="button dashboard-button">Return to main dashboard</button>
                </form>
                <form action="" method="POST" style="display: inline-block;">
                    <input type="hidden" name="profile_id" value="<?php echo htmlspecialchars($this->profileData->profile_id); ?>">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($this->profileData->username); ?>">
                    <button type="submit" name="update" class="button">Update account profile</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// MAIN LOGIC: Sets up components and renders the view
$accountController = new ViewBuyerAccountController();
$profileData = $accountController->getProfile($username);

// Render the view with retrieved profile data
$userAccount = new ViewBuyerAccountPage($profileData);
$userAccount->render();
?>
