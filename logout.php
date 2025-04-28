<?php
session_start();

// ENTITY LAYER: Handles session data and state
class SessionManager {
    /**
     * Clears all session data
     */
    public function clearSession() {
        session_unset();
        session_destroy();
    }
}

// CONTROL LAYER: Manages logout process
class LogoutController {
    private $sessionManager;

    public function __construct() {
        $this->sessionManager = new SessionManager();
    }

    /**
     * Handles the logout process
     * @return bool True if logout was successful
     */
    public function handleLogout() {
        $this->sessionManager->clearSession();
        return true;
    }
}

// BOUNDARY LAYER: Handles user interface and form processing
class LogoutPage {
    private $controller;

    public function __construct() {
        $this->controller = new LogoutController();
    }

    /**
     * Renders the logout confirmation UI
     */
    public function LogoutUI() {
        ?>
        <!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Logout Confirmation</title>
            <link rel="stylesheet" href="./style.css">
            <style>
                :root {
                    --color-white: #ffffff;
                    --color-black: #000000;
                    --color-primary: #007BFF;
                    --color-primary-dark: #0056b3;
                    --shadow-lg: 0 10px 20px rgba(0, 0, 0, 0.2);
                }

                body, html {
                    height: 100%;
                    margin: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: #f9f9f9;
                    font-family: Arial, sans-serif;
                }

                .logout-container {
                    max-width: 500px;
                    width: 90%;
                    padding: 0 1rem;
                }

                .logout-card {
                    background-color: var(--color-white);
                    padding: 2.5rem;
                    border-radius: 1rem;
                    box-shadow: var(--shadow-lg);
                    text-align: center;
                }

                .logout-card h1 {
                    color: var(--color-black);
                    font-size: 1.875rem;
                    margin-bottom: 1.5rem;
                }

                .return-btn {
                    background-color: var(--color-primary);
                    color: var(--color-white);
                    padding: 0.75rem 1.5rem;
                    border: none;
                    border-radius: 0.5rem;
                    font-size: 1rem;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background-color 0.2s;
                    margin-top: 1.5rem;
                }

                .return-btn:hover {
                    background-color: var(--color-primary-dark);
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
            </style>
        </head>
        <body>
            <a href="index.html" class="logo"><h3>clean.sg</h3></a>

            <div class="logout-container">
                <div class="logout-card">
                    <h1>You have been successfully logged out.</h1>
                    <form method="post" action="login.php">
                        <button type="submit" class="return-btn">Return to Login</button>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Handles the logout process and renders the UI
     */
    public function handleLogout() {
        $this->controller->handleLogout();
        $this->LogoutUI();
    }
}

// Main execution
$page = new LogoutPage();
$page->handleLogout();
?>
