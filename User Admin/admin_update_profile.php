<?php
require '../connectDatabase.php';
session_start();

// BOUNDARY LAYER: HTML View for managing user accounts
class ViewUserProfilePage
{
    private $controller;
    private $users; // Store fetched users
    private $about; // Store about information
    private $role_id; // Store role_id for UI use

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function handleRequest()
    {
        $this->role_id = isset($_GET['role_id']) ? $_GET['role_id'] : '';
        $this->users = $this->controller->getUsersByProfile($this->role_id);
        $this->about = $this->controller->getAbout();
        $this->ViewUserProfileUI();
    }

    public function ViewUserProfileUI()
    {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>User Profile</title>
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

                #main-table {
                    border-collapse: collapse;
                    width: 90%;
                    max-width: 1000px;
                    margin: 20px auto;
                    background-color: #161b22;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                }

                #main-table th, #main-table td {
                    padding: 14px 18px;
                    text-align: center;
                    font-size: 16px;
                    border-bottom: 1px solid #21262d;
                }

                #main-table th {
                    background-color: #21262d;
                    color: #58a6ff;
                    font-weight: 600;
                    font-size: 1rem;
                }

                #main-table tr:hover {
                    background-color: #30363d;
                }

                .button-font {
                    font-size: 14px;
                    padding: 8px 16px;
                    margin: 2px;
                    border-radius: 6px;
                    border: none;
                    cursor: pointer;
                    transition: background 0.3s;
                }

                .update-button {
                    background-color: #238636;
                    color: white;
                    text-decoration: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    display: inline-block;
                    transition: background-color 0.3s;
                }

                .update-button:hover {
                    background-color: #2ea043;
                }

                .return-button {
                    font-size: 16px;
                    padding: 12px 24px;
                    background-color:hsl(0, 0.00%, 50%);
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                    margin-top: 30px;
                }

                .return-button:hover {
                    background-color:hsl(0, 0.00%, 50%);
                }

                .no-data {
                    text-align: center;
                    color: #f85149;
                }

                .center-button {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-top: 20px;
                }

                @media (max-width: 768px) {
                    #main-table {
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
                <h1>Users in this Role</h1>
                <table id="main-table">
                    <tr>
                        <th>UserID</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Role Description</th>
                        <th>Action</th>
                    </tr>
                    <?php if (!empty($this->users)): ?>
                        <?php foreach ($this->users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['status_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['role_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['role_description'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="admin_update_profile_details.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="update-button">Update</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">No users found for this role.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <div class="center-button">
                    <form method="post" action="admin_manage_user_profiles.php">
                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($this->role_id); ?>">
                        <input type="submit" value="Return" class="return-button">
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// CONTROL LAYER: Manages data retrieval and updates based on Boundary's requests
class ViewUserProfileController
{
    private $userProfile;

    public function __construct($userProfile)
    {
        $this->userProfile = $userProfile;
    }

    public function getUsersByProfile($role_id)
    {
        return $this->userProfile->getUsersByProfile($role_id);
    }

    public function getAbout()
    {
        return $this->userProfile->getAbout();
    }
}

// ENTITY LAYER: UserProfile handles all database interactions and data logic
class UserProfile {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getUsersByProfile($role_id = '') {
        $query = "
            SELECT u.user_id, u.username, s.status_name, r.role_name, r.role_description
            FROM users u
            JOIN role r ON u.role_id = r.role_id
            JOIN status s ON u.status_id = s.status_id";
        
        if (!empty($role_id)) {
            $query .= " WHERE u.role_id = ?";
        }

        $stmt = $this->mysqli->prepare($query);

        if (!empty($role_id)) {
            $stmt->bind_param('i', $role_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $userProfile = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        
        return $userProfile;
    }

    public function getAbout() {
        $query = "SELECT about FROM profile LIMIT 1"; // Assume one general 'about' section if applicable
        $result = $this->mysqli->query($query);
        return $result->fetch_assoc()['about'] ?? ''; // Return 'about' or empty string if not found
    }
}

// MAIN LOGIC: Initialize components and delegate request handling to the view
$database = new Database();
$mysqli = $database->getConnection();

$userProfile = new UserProfile($mysqli);
$controller = new ViewUserProfileController($userProfile);
$view = new ViewUserProfilePage($controller);
$view->handleRequest();

$database->closeConnection();
?> 