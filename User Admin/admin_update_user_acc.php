<?php
require '../connectDatabase.php';

// ENTITY LAYER: Handles data structure and database interaction for User Account
class UserAccount {
    private $conn;
    public $user_id;
    public $username;
    public $password;
    public $role_id;
    public $email;
    public $phone_num;
    public $status_id;

    public function __construct($userData = null) {
        // Initialize database connection
        $database = new Database();
        $this->conn = $database->getConnection();

        // If user data is provided, initialize properties
        if ($userData) {
            $this->user_id = $userData['user_id'];
            $this->username = $userData['username'];
            $this->password = $userData['password'];
            $this->role_id = $userData['role_id'];
            $this->email = $userData['email'];
            $this->phone_num = $userData['phone_num'];
            $this->status_id = $userData['status_id'];
        }
    }

    public function getUserDetails($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();

        if ($userData) {
            $this->user_id = $userData['user_id'];
            $this->username = $userData['username'];
            $this->password = $userData['password'];
            $this->role_id = $userData['role_id'];
            $this->email = $userData['email'];
            $this->phone_num = $userData['phone_num'];
            $this->status_id = $userData['status_id'];
            return $this;
        }
        return null;
    }

    public function updateUserAccount($userAccount) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET username = ?, password = ?, role_id = ?, email = ?, phone_num = ? WHERE user_id = ?"
        );
        $stmt->bind_param(
            "ssissi",
            $userAccount['username'],
            $userAccount['password'],
            $userAccount['role_id'],
            $userAccount['email'],
            $userAccount['phone_num'],
            $userAccount['user_id']
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getRoles() {
        $roles = [];
        $stmt = $this->conn->prepare("SELECT role_id, role_name FROM role");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        $stmt->close();
        return $roles;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// CONTROL LAYER: Manages account updates and data retrieval
class UpdateUserAccountController {
    private $useraccount;

    public function __construct() {
        $this->useraccount = new UserAccount();
    }

    public function getUserAccount($username) {
        return $this->useraccount->getUserDetails($username);
    }

    public function getRoles() {
        return $this->useraccount->getRoles();
    }

    public function handleAccountUpdate($userAccount) {
        return $this->useraccount->updateUserAccount($userAccount);
    }

    
}

// BOUNDARY LAYER: Renders the form and handles form submission
class UpdateUserAccountPage {
    private $controller;

    public function __construct() {
        $this->controller = new UpdateUserAccountController();
    }

    public function handleFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userAccount = $_POST;
            $updateSuccess = $this->controller->handleAccountUpdate($userAccount);
            

            if ($updateSuccess) {
                header("Location: admin_manage_user_acc.php");
                exit();
            } else {
                echo "Error updating user account.";
            }
        }
    }

    public function UpdateUserAccountUI() {
        if (!isset($_GET['username'])) {
            header("Location: admin_manage_user_acc.php");
            exit();
        }

        $username = $_GET['username'];
        $userAccount = $this->controller->getUserAccount($username);
        $roles = $this->controller->getRoles();
        

        if (!$userAccount) {
            die("User not found.");
        }
        
        ?>
        <html>
        <head>
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
                    font-size: 36px;
                    color: #58a6ff;
                    text-align: center;
                    margin-bottom: 20px;
                    font-weight: 600;
                }

                .form-container {
                    background-color: #161b22;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.7);
                    max-width: 800px;
                    margin: 0 auto;
                }

                .form-container table {
                    width: 100%;
                    margin-bottom: 20px;
                }

                .form-container td {
                    padding: 14px 18px;
                    font-size: 16px;
                }

                .form-container label {
                    display: block;
                    font-weight: 500;
                    color: #c9d1d9;
                }

                .form-container input, .form-container select {
                    width: 100%;
                    padding: 10px;
                    margin: 5px 0 15px;
                    border-radius: 6px;
                    border: 1px solid #30363d;
                    background-color: #0d1117;
                    color: #c9d1d9;
                }

                .form-container input:focus, .form-container select:focus {
                    outline: none;
                    border-color: #58a6ff;
                }

                .buttons-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 15px;
                    margin-top: 20px;
                }

                .form-container button {
                    background-color: #238636;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background 0.3s;
                    width: 200px;
                }

                .form-container button:hover {
                    background-color: #2ea043;
                }

                .return-button {
                    display: inline-block;
                    background-color: hsl(0, 0%, 50%);
                    color: white;
                    text-decoration: none;
                    padding: 12px 24px;
                    border-radius: 6px;
                    text-align: center;
                    font-size: 16px;
                    transition: background 0.3s;
                    width: 200px;
                }

                .return-button:hover {
                    background-color: hsl(0, 0%, 40%);
                }

                @media (max-width: 768px) {
                    .form-container {
                        width: 95%;
                        padding: 15px;
                    }
                    h1 {
                        font-size: 1.8rem;
                    }
                }
            </style>
        </head>
        <body>
            <h1>Update User Account</h1>
            <div class="form-container">
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userAccount->user_id); ?>" />
                    <table>
                        <tr>
                            <td><label for="username">Username:</label></td>
                            <td><input type="text" id="username" name="username" value="<?= htmlspecialchars($userAccount->username); ?>" required /></td>
                        </tr>
                        <tr>
                            <td><label for="password">Password:</label></td>
                            <td><input type="password" id="password" name="password" value="<?= htmlspecialchars($userAccount->password); ?>" required /></td>
                        </tr>
                        <tr>
                            <td><label for="role_id">Role:</label></td>
                            <td>
                                <select id="role_id" name="role_id">
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['role_id']; ?>" <?= $userAccount->role_id == $role['role_id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($role['role_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="email">Email:</label></td>
                            <td><input type="email" id="email" name="email" value="<?= htmlspecialchars($userAccount->email); ?>" required /></td>
                        </tr>
                        <tr>
                            <td><label for="phone_num">Phone:</label></td>
                            <td><input type="text" id="phone_num" name="phone_num" value="<?= htmlspecialchars($userAccount->phone_num); ?>" required /></td>
                        </tr>
                    </table>
                    
                    <div class="buttons-container">
                        <button type="submit">Update Account</button>
                        <a href="admin_manage_user_acc.php" class="return-button">Return</a>
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// Instantiate the Boundary class and handle the form submission
$view = new UpdateUserAccountPage();
$view->handleFormSubmission();
$view->UpdateUserAccountUI();
?>