<?php
session_start(); // Start the session
require '../connectDatabase.php';

// Entity Layer
class User {
    private $username;

    public function __construct($username) {
        $this->username = htmlspecialchars($username); // Sanitize username input
    }

    public function getUsername() {
        return $this->username;
    }
}

// Control Layer
class DashboardController {
    public function getUsernameFromSession() {
        return $_SESSION['username'] ?? null;
    }
}

// Boundary Layer
class DashboardView {
    private $controller;
    private $username;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->username = ''; // Initial state; no user set until initialized
    }

    public function handleRequest() {
        // Ensure the user is logged in
        $username = $this->controller->getUsernameFromSession();
        if (!$username) {
            header("Location: login.php");
            exit();
        }

        // Create a User entity and set the username in the view
        $user = new User($username);
        $this->setUsername($user->getUsername());

        // Process form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
        }

        // Render the dashboard view
        $this->render();
    }

    public function setUsername($username) {
        $this->username = htmlspecialchars($username); // Sanitize when setting username
    }

    private function handleFormSubmission() {
        if (isset($_POST['userAcc'])) {
            $this->redirectTo('admin_manage_user_acc.php');
        } elseif (isset($_POST['userProfile'])) {
            $this->redirectTo('admin_manage_user_profiles.php');
        } elseif (isset($_POST['logout'])) {
            $this->redirectTo('../logout.php');
        }
    }

    private function render() {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Dashboard</title>
            <style>
    body {
        font-family: 'Poppins', 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #0d1117;
        color: #c9d1d9;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .headDiv {
        background-color: #161b22;
        padding: 40px 20px;
        text-align: center;
        border-bottom: 1px solid #30363d;
    }

    .header {
        font-size: 2rem;
        margin: 0;
        margin-bottom: 10px;
        color: #58a6ff;
    }

    h2 {
        font-weight: 300;
        font-size: 1.2rem;
        margin: 0;
        color: #8b949e;
    }

    .mainInterface {
        background: #161b22;
        margin: 40px auto;
        padding: 30px 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.6);
        flex-grow: 1;
    }

    .formBody {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    button, input[type="submit"] {
        background-color: #21262d;
        border: 1px solid #30363d;
        padding: 14px 18px;
        border-radius: 8px;
        color: #c9d1d9;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    button:hover, input[type="submit"]:hover {
        background-color: #30363d;
        transform: translateY(-1px);
        border-color: #58a6ff;
    }

    .logout-button {
        background-color: #da3633;
        border: 1px solid #f85149;
    }

    .logout-button:hover {
        background-color: #f85149;
        transform: translateY(-1px);
    }

    footer {
        text-align: center;
        padding: 20px;
        font-size: 0.8rem;
        color: #8b949e;
    }

    @media (max-width: 600px) {
        .mainInterface {
            margin: 20px;
            padding: 20px;
        }
    }
</style>


        </head>
        <body>
            <div class="headDiv">
                <h1 class="header">Welcome to the Admin Dashboard, <?php echo $this->username; ?>!</h1>
                <h2>What would you like to do today?</h2>
            </div>

            <div class="mainInterface">
                <form method="post" class="formBody">
                    <button type="submit" name="userAcc">Manage User Accounts</button>
                    <button type="submit" name="userProfile">Manage User Profiles</button>
                    <input type="submit" class="logout-button" value="Logout" name="logout">
                </form>
            </div>
        </body>
        </html>
        <?php
    }

    private function redirectTo($location) {
        header("Location: $location");
        exit();
    }
}

// Main logic: Instantiate the boundary and controller
$controller = new DashboardController();
$dashboardView = new DashboardView($controller);
$dashboardView->handleRequest();
?>
