<?php
require '../connectDatabase.php';
session_start();

// BOUNDARY LAYER: HTML View for managing user accounts
class UserProfilePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        $action = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

        if (isset($action['createProfile'])) {
            header("Location: ProfileCreation.php");
            exit();
        }

        if (isset($action['viewProfile'])) {
            $username = $action['username'];
            $role_id = $action['role_id'];
            header("Location: admin_view_profile.php?username=" . urlencode($username) . "&role_id=" . urlencode($role_id));
            exit();
        }

        if (isset($action['updateProfile'])) {
            $role_id = $action['role_id'];
            header("Location: admin_update_profile.php?role_id=" . urlencode($role_id));
            exit();
        }

        if (isset($action['suspendProfile'])) {
            $username = $action['username'];
            $role_id = $action['role_id'];
            header("Location: admin_suspend_user_profiles.php?username=" . urlencode($username) . "&role_id=" . urlencode($role_id));
            exit();
        }

        // Render the profile management view
        $this->ManageUserProfileUI();
    }

    public function ManageUserProfileUI() {
        $profiles = $this->controller->getProfiles();
        $roles = $this->controller->getRoles();
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Manage User Profiles</title>
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
                .select-label {
                    font-size: 18px;
                    color: #8b949e;
                    margin-right: 10px;
                }
                #role_id {
                    font-size: 16px;
                    padding: 10px;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    border: 1px solid #30363d;
                    border-radius: 8px;
                }
                #role_id:focus {
                    outline: none;
                    border-color: #58a6ff;
                    background-color: #161b22;
                }
                #search {
                    font-size: 16px;
                    padding: 10px;
                    background-color: #161b22;
                    color: #c9d1d9;
                    border: 1px solid #30363d;
                    border-radius: 8px;
                }
                form {
                    text-align: center;
                    margin-bottom: 20px;
                }
                /* General button style */
                .button-font {
                    font-size: 14px;
                    padding: 8px 16px;
                    margin: 2px;
                    border-radius: 6px;
                    border: none;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                /* Specific button colors */
                .create-button {
                    background-color: #ff8c00; /* orange */
                    color: white;
                }
                .create-button:hover {
                    background-color: #ffa500;
                }
                .view-button {
                    background-color: #238636; /* green */
                    color: white;
                }
                .view-button:hover {
                    background-color: #2ea043;
                }
                .update-button {
                    background-color: #1f6feb; /* blue */
                    color: white;
                }
                .update-button:hover {
                    background-color: #388bfd;
                }
                .suspend-button {
                    background-color: #da3633; /* red */
                    color: white;
                }
                .suspend-button:hover {
                    background-color: #f85149;
                }
                /* Group buttons tighter */
                .button-group {
                    display: flex;
                    justify-content: center;
                    gap: 6px;
                    flex-wrap: wrap;
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
            <h1>Manage User Profiles</h1>

            <form method="post" action="admin_search_user_profiles.php" style="text-align: center; margin: 20px 0;">
                <button type="submit" class="button-font" style="background-color: #58a6ff; color: white; padding: 10px 20px; font-size: 16px;">Search Users</button>
            </form>

            <table id="main-table">
                <tr>
                    <th>Profile</th>
                    <th>Status</th>
                    <th>Number of Accounts</th>
                    <th>Actions</th>
                </tr>
                <?php if (!empty($profiles)): ?>
                    <?php foreach ($profiles as $profile): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($profile['role_name']); ?></td>
                            <td><?php echo htmlspecialchars($profile['status_name']); ?></td>
                            <td><?php echo htmlspecialchars($profile['account_count']); ?></td>
                            <td>
                                <div class="button-group">
                                    <form method="post" action="admin_create_user_profile.php">
                                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($profile['role_id']); ?>">
                                        <button type="submit" class="button-font create-button" name="createProfile">Create</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>">
                                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($profile['role_id']); ?>">
                                        <button type="submit" class="button-font view-button" id="viewProfile" name="viewProfile">View</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($profile['role_id']); ?>">
                                        <button type="submit" class="button-font update-button" id="updateProfile" name="updateProfile">Update</button>
                                    </form>
                                    <form method="post" action="">
                                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>">
                                        <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($profile['role_id']); ?>">
                                        <button type="submit" class="button-font suspend-button" id="suspendProfile" name="suspendProfile">Suspend</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No profiles found.</td>
                    </tr>
                <?php endif; ?>
            </table>

            <form method="post" action="admin_dashboard.php">
                <input type="submit" value="Return" class="return-button">
            </form>
        </body>
        </html>
        <?php
    }
}

// CONTROL LAYER: Manages data retrieval and updates based on Boundary's requests
class UserProfileDashboardController {
    private $userProfile;

    public function __construct($userProfile) {
        $this->userProfile = $userProfile;
    }

    public function getProfiles($role_name = '') {
        return $this->userProfile->getAllProfiles($role_name);
    }

    public function getRoles() {
        return $this->userProfile->getAllRoles();
    }
}

// ENTITY LAYER: UserProfile handles all database interactions and data logic
class UserProfile {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getAllProfiles($role_name = '') {
        $query = "SELECT u.username, r.role_id, r.role_name, 
                         IFNULL(s.status_name, 'No Status') AS status_name, 
                         COUNT(u.user_id) AS account_count
                  FROM role r
                  LEFT JOIN users u ON r.role_id = u.role_id
                  LEFT JOIN status s ON s.status_id = u.status_id";
    
        if (!empty($role_name)) {
            $query .= " WHERE r.role_name = ?";
        }
    
        $query .= " GROUP BY r.role_id, s.status_name";
    
        $stmt = $this->mysqli->prepare($query);
    
        if (!empty($role_name)) {
            $stmt->bind_param('s', $role_name);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $profiles = [];
        while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }
    
        $stmt->close();
        return $profiles;
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
$userController = new UserProfileDashboardController($userProfileEntity);
$userProfileView = new UserProfilePage($userController);
$userProfileView->handleRequest();

$database->closeConnection();
?>
