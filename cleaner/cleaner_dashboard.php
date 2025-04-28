<?php
session_start();

// Entity layer
class UserAccount {
    private $username;

    public function __construct($username) {
        $this->username = htmlspecialchars($username);
    }

    public function getUsername() {
        return $this->username;
    }
}

// Boundary layer
class DashboardView {
    private $username;

    public function setUsername($username) { // Method to set username
        $this->username = $username;
    }

    public function render() {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cleaner Dashboard</title>
            <link rel="stylesheet" href="../style.css">
            <style>
                .dashboard-container {
                    max-width: 800px;
                    margin: 2rem auto;
                    padding: 0 1rem;
                }

                .dashboard-header {
                    background-color: var(--color-white);
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-md);
                    margin-bottom: 2rem;
                    text-align: center;
                }

                .dashboard-header h1 {
                    color: var(--color-black);
                    font-size: 1.875rem;
                    margin-bottom: 0.5rem;
                }

                .dashboard-header h2 {
                    color: var(--color-gray);
                    font-size: 1.25rem;
                    font-weight: 400;
                }

                .dashboard-actions {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                    margin-top: 2rem;
                }

                .action-button {
                    background-color: var(--color-white);
                    border: none;
                    padding: 1.5rem;
                    border-radius: 0.75rem;
                    box-shadow: var(--shadow-md);
                    cursor: pointer;
                    transition: all 0.2s;
                    text-align: center;
                    color: var(--color-black);
                    font-size: 1.125rem;
                    font-weight: 500;
                    width: 100%;
                }

                .action-button:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--shadow-lg);
                }

                .action-button.logout {
                    background-color: #fee2e2;
                    color: #dc2626;
                }

                .action-button.logout:hover {
                    background-color: #fecaca;
                }

                .logo {
                    position: fixed;
                    top: 1rem;
                    left: 1rem;
                    z-index: 1000;
                }

                .logo h3 {
                    color: var(--color-primary);
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin: 0;
                }

                @media (max-width: 640px) {
                    .dashboard-actions {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <a href="../index.html" class="logo"><h3>clean.sg</h3></a>

            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h1>Welcome, <?php echo $this->username; ?>!</h1>
                    <h2>What would you like to do today?</h2>
                </div>

                <form method="post" class="dashboard-actions">
                    <button type="submit" id="manageProfile" name="manageProfile" class="action-button">
                        View/Update Profile
                    </button>
                    <button type="submit" id="view" name="view" class="action-button">
                        View My Listings
                    </button>
                    <button type="submit" id="viewMatches" name="viewMatches" class="action-button">
                        View My Matches
                    </button>
                    <button type="submit" id="logout" name="logout" class="action-button logout">
                        Logout
                    </button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// Control layer
class DashboardController {
    private $view;
    private $agent;

    public function __construct(UserAccount $agent) {
        $this->agent = $agent;
        $this->view = new DashboardView();
        $this->view->setUsername($this->agent->getUsername()); // Set the username in the view
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['manageProfile'])) {
                header("Location: cleaner_manage_profile.php");
                exit();
            }

            if (isset($_POST['view'])) {
                $username = urlencode($this->agent->getUsername());
                header("Location: cleaner_view_listings.php?username=" . $username);
                exit();
            }

            if (isset($_POST['viewMatches'])) {
                header("Location: cleaner_view_matches.php");
                exit();
            }

            if (isset($_POST['logout'])) {
                header("Location: ../logout.php");
                exit();
            }
        }

        // Render the view without parameters
        $this->view->render();
    }
}

// Main logic
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$agent = new UserAccount($username);
$controller = new DashboardController($agent);
$controller->handleRequest();
?>
