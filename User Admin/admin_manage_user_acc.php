<?php
// Connect to the database
require '../connectDatabase.php';

// ENTITY LAYER: Represents user data and interacts with the database
class UserAccount
{
    public $username;
    public $role_name;
    public $status_name;

    private static $database;

    public function __construct($username, $role_name, $status_name)
    {
        $this->username = htmlspecialchars($username);
        $this->role_name = htmlspecialchars($role_name);
        $this->status_name = htmlspecialchars($status_name);
    }

    public static function setDatabase($database)
    {
        self::$database = $database;
    }

    public static function fetchUsers()
    {
        $query = "SELECT u.username, r.role_name, s.status_name
                  FROM users u
                  JOIN role r ON u.role_id = r.role_id
                  JOIN status s ON u.status_id = s.status_id";
        $result = self::$database->getConnection()->query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row['username'], $row['role_name'], $row['status_name']);
        }
        return $users;
    }

    public static function searchUserAccount($role, $username)
    {
        $query = "SELECT u.username, r.role_name, s.status_name
                  FROM users u
                  JOIN role r ON u.role_id = r.role_id
                  JOIN status s ON u.status_id = s.status_id
                  WHERE 1=1";

        if (!empty($role)) {
            $query .= " AND r.role_name = '" . self::$database->getConnection()->real_escape_string($role) . "'";
        }
        if (!empty($username)) {
            $query .= " AND u.username LIKE '%" . self::$database->getConnection()->real_escape_string($username) . "%'";
        }

        $result = self::$database->getConnection()->query($query);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row['username'], $row['role_name'], $row['status_name']);
        }
        return $users;
    }

    public static function fetchRoles()
    {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1";
            $username = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') ?: "";
            $dbname = getenv('DB_NAME') ?: "csit314";
            $port = getenv('DB_PORT') ?: 3307;

            $connection = new mysqli($servername, $username, $password, $dbname, $port);

            if ($connection->connect_error) {
                throw new Exception("Connection failed: " . $connection->connect_error);
            }

            $roles = [];
            $sql = "SELECT DISTINCT role_name FROM role";

            if ($result = $connection->query($sql)) {
                while ($row = $result->fetch_assoc()) {
                    $roles[] = $row['role_name'];
                }
                $result->free();
            }

            $connection->close();
            return $roles;
        } catch (Exception $e) {
            error_log("Error in fetchRoles: " . $e->getMessage());
            return [];
        }
    }
}

// CONTROLLER LAYER: Manage data flow between Boundary and Entity layers
class SearchUserAccountController
{
    private $users;

    public function __construct()
    {
        $this->users = [];
    }

    public function getUsers()
    {
        $this->users = UserAccount::fetchUsers();
        return $this->users;
    }

    public function searchUserAccounts($role, $username)
    {
        $this->users = UserAccount::searchUserAccount($role, $username);
        return $this->users;
    }

    public function getRoles()
    {
        return UserAccount::fetchRoles();
    }
}

// BOUNDARY LAYER: HTML View for managing user accounts and handling user interactions
class SearchUserAccountPage
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function SearchUserAccountUI()
    {
        $users = $this->controller->getUsers();
        $roles = $this->controller->getRoles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchButton'])) {
            $role = $_POST['role'];
            $username = $_POST['search'];
            $users = $this->controller->searchUserAccounts($role, $username);
        }
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Manage User Accounts</title>
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
                table {
                    border-collapse: collapse;
                    width: 90%;
                    max-width: 1000px;
                    margin: 20px auto;
                    background-color: #161b22;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                }
                th, td {
                    padding: 14px 18px;
                    text-align: center;
                    font-size: 16px;
                    border-bottom: 1px solid #21262d;
                }
                th {
                    background-color: #21262d;
                    color: #58a6ff;
                    font-weight: 600;
                    font-size: 1rem;
                }
                tr:hover {
                    background-color: #30363d;
                }
                form {
                    margin: 10px auto;
                    text-align: center;
                }
                input[type="text"], select {
                    padding: 8px;
                    font-size: 16px;
                    margin: 5px;
                    border: 1px solid #30363d;
                    border-radius: 6px;
                    width: 200px;
                    background-color: #0d1117;
                    color: #c9d1d9;
                }
                input[type="text"]:focus, select:focus {
                    outline: none;
                    border-color: #58a6ff;
                }
                button[type="submit"], input[type="submit"] {
                    padding: 10px 15px;
                    font-size: 16px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    margin: 5px;
                    transition: background 0.3s;
                }
                .button-font {
                    padding: 8px 14px;
                    margin: 2px;
                    font-size: 14px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                .btn-view {
                    background-color: #238636;
                    color: white;
                }
                .btn-update {
                    background-color: #1f6feb;
                    color: white;
                }
                .btn-suspend {
                    background-color: #da3633;
                    color: white;
                }
                .btn-create {
                    background-color: #238636;
                    color: white;
                }
                .btn-create:hover {
                    background-color: #2ea043;
                }
                .btn-view:hover {
                    background-color: #2ea043;
                }
                .btn-update:hover {
                    background-color: #388bfd;
                }
                .btn-suspend:hover {
                    background-color: #f85149;
                }
                .return-btn {
                    background-color:hsl(0, 0.00%, 50%);
                    color: white;
                    font-size: 16px;
                    padding: 12px 24px;
                    text-decoration: none;
                    border-radius: 6px;
                    margin-top: 20px;
                    display: block;
                    width: 180px;
                    margin-left: auto;
                    margin-right: auto;
                    text-align: center;
                    transition: background 0.3s;
                }
                .return-btn:hover {
                    background-color:hsl(0, 0.00%, 50%);
                }
                @media (max-width: 768px) {
                    table {
                        width: 95%;
                    }
                    h1 {
                        font-size: 1.8rem;
                    }
                }
            </style>
        </head>
        <body>
            <h1>Manage User Accounts</h1>

            <form method="POST">
                <label for="role" class="select-label">Filter by Role:</label>
                <select id="role" name="role">
                    <option value="">All roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo htmlspecialchars($role); ?>"><?php echo htmlspecialchars($role); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter username" />
                <button type="submit" name="searchButton" class="btn-view">Search</button>
            </form>

            <form method="post" action="accountCreation.php" style="text-align:center;">
                <button type="submit" name="createAccount" class="btn-create">Create New User Account</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user->username; ?></td>
                                <td><?php echo $user->role_name; ?></td>
                                <td><?php echo $user->status_name; ?></td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="username" value="<?php echo $user->username; ?>">
                                        <button type="submit" name="viewAccount" class="button-font btn-view">View</button>
                                        <button type="submit" name="updateAccount" class="button-font btn-update">Update</button>
                                        <button type="submit" name="suspendAccount" class="button-font btn-suspend">Suspend</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="admin_dashboard.php" class="return-btn">Return</a>
        </body>
        </html>
        <?php
    }

    public function handleUserInteractions()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['createAccount'])) {
                header("Location: accountCreation.php");
                exit();
            } elseif (isset($_POST['viewAccount'])) {
                $username = $_POST['username'];
                header("Location: admin_view_account.php?username=" . urlencode($username));
                exit();
            } elseif (isset($_POST['updateAccount'])) {
                $username = $_POST['username'];
                header("Location: admin_update_user_acc.php?username=" . urlencode($username));
                exit();
            } elseif (isset($_POST['suspendAccount'])) {
                $username = $_POST['username'];
                header("Location: admin_suspend_user_acc.php?username=" . urlencode($username));
                exit();
            }
        }
    }
}

// MAIN EXECUTION
$database = new Database();
UserAccount::setDatabase($database);
$controller = new SearchUserAccountController();
$page = new SearchUserAccountPage($controller);
$page->handleUserInteractions();
$page->SearchUserAccountUI();
?>
