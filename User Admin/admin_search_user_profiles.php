<?php
require '../connectDatabase.php';
session_start();

// BOUNDARY LAYER: HTML View for searching user profiles
class SearchUserProfilePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        // Render the search profile view
        $this->SearchUserProfileUI();
    }

    public function SearchUserProfileUI() {
        $profiles = [];
        $searchTerm = '';
        $selectedRole = '';

        // Always get profiles, even without search
        $profiles = $this->controller->searchProfiles($searchTerm, $selectedRole);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
            $searchTerm = $_POST['searchTerm'] ?? '';
            $selectedRole = $_POST['role_id'] ?? '';
            $profiles = $this->controller->searchProfiles($searchTerm, $selectedRole);
        }

        $roles = $this->controller->getRoles();
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Search User Profiles</title>
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
                .search-container {
                    width: 90%;
                    max-width: 1000px;
                    margin: 20px auto;
                    background-color: #161b22;
                    border-radius: 10px;
                    padding: 20px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                }
                .search-form {
                    display: flex;
                    gap: 15px;
                    flex-wrap: wrap;
                    justify-content: center;
                    align-items: center;
                }
                .search-input {
                    flex: 1;
                    min-width: 200px;
                    padding: 10px;
                    font-size: 16px;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    border: 1px solid #30363d;
                    border-radius: 8px;
                }
                .search-input:focus {
                    outline: none;
                    border-color: #58a6ff;
                }
                #role_id {
                    padding: 10px;
                    font-size: 16px;
                    background-color: #0d1117;
                    color: #c9d1d9;
                    border: 1px solid #30363d;
                    border-radius: 8px;
                }
                .search-button {
                    padding: 10px 20px;
                    font-size: 16px;
                    background-color: #238636;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }
                .search-button:hover {
                    background-color: #2ea043;
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
                    margin-left: auto;
                    margin-right: auto;
                }
                #main-table th, #main-table td {
                    padding: 14px 18px;
                    text-align: center;
                    vertical-align: middle;
                    font-size: 16px;
                    border-bottom: 1px solid #21262d;
                }
                #main-table th {
                    background-color: #21262d;
                    color: #58a6ff;
                    font-weight: 600;
                }
                #main-table tr:hover {
                    background-color: #30363d;
                }
                .return-button {
                    font-size: 16px;
                    padding: 12px 24px;
                    background-color: hsl(0, 0.00%, 50%);
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                    margin: 20px auto;
                    display: block;
                }
                .return-button:hover {
                    background-color: hsl(0, 0.00%, 50%);
                }
                @media (max-width: 768px) {
                    .search-container, #main-table {
                        width: 95%;
                    }
                    .search-form {
                        flex-direction: column;
                    }
                    .search-input, #role_id {
                        width: 100%;
                    }
                }
            </style>
        </head>
        <body>
            <h1>Search User Profiles</h1>

            <div class="search-container">
                <form method="post" class="search-form">
                    <input type="text" name="searchTerm" class="search-input" placeholder="Search by username" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <select name="role_id" id="role_id">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role['role_id']); ?>" <?php echo $selectedRole == $role['role_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($role['role_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="search" class="search-button">Search</button>
                </form>
            </div>

            <table id="main-table">
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
                <?php if (!empty($profiles)): ?>
                    <?php foreach ($profiles as $profile): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($profile['username']); ?></td>
                            <td><?php echo htmlspecialchars($profile['role_name']); ?></td>
                            <td><?php echo htmlspecialchars($profile['status_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No profiles found.</td>
                    </tr>
                <?php endif; ?>
            </table>

            <form method="post" action="admin_manage_user_profiles.php">
                <input type="submit" value="Return" class="return-button">
            </form>
        </body>
        </html>
        <?php
    }
}

// CONTROL LAYER: Manages data retrieval and updates based on Boundary's requests
class SearchUserProfileController {
    private $userProfile;

    public function __construct($userProfile) {
        $this->userProfile = $userProfile;
    }

    public function searchProfiles($searchTerm, $roleId) {
        return $this->userProfile->searchProfiles($searchTerm, $roleId);
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

    public function searchProfiles($searchTerm, $roleId) {
        $query = "SELECT u.username, r.role_id, r.role_name, 
                         IFNULL(s.status_name, 'No Status') AS status_name
                  FROM users u
                  JOIN role r ON u.role_id = r.role_id
                  LEFT JOIN status s ON s.status_id = u.status_id
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($searchTerm)) {
            $query .= " AND (u.username LIKE ? OR r.role_name LIKE ?)";
            $searchPattern = "%" . $searchTerm . "%";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $types .= "ss";
        }

        if (!empty($roleId)) {
            $query .= " AND r.role_id = ?";
            $params[] = $roleId;
            $types .= "i";
        }

        $query .= " ORDER BY u.username";

        $stmt = $this->mysqli->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
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
$userController = new SearchUserProfileController($userProfileEntity);
$userProfileView = new SearchUserProfilePage($userController);
$userProfileView->handleRequest();

$database->closeConnection();
?> 