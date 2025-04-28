<?php
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

/** ENTITY LAYER */
class CleaningService {
    private $db;

    public function __construct() {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1"; // Use IP to avoid socket issues
            $username = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') ?: "";
            $dbname = getenv('DB_NAME') ?: "csit314";
            $port = getenv('DB_PORT') ?: 3307;
    
            $dsn = "mysql:host={$servername};port={$port};dbname={$dbname}";
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function createCleaningService($formData, $username) {
        // Retrieve the user_id based on the username
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $cleaner_id = $user['user_id'];
        } else {
            throw new Exception("User not found.");
        }

        // Insert the cleaning service
        $stmt = $this->db->prepare("INSERT INTO cleaningservices (cleaner_id, service_title, service_description, service_type, service_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $cleaner_id);
        $stmt->bindParam(2, $formData['service_title']);
        $stmt->bindParam(3, $formData['service_description']);
        $stmt->bindParam(4, $formData['service_type']);
        $stmt->bindParam(5, $formData['service_price']);

        return $stmt->execute(); //returns a boolean
    }
}

/** CONTROL LAYER */
class CleaningServiceController {
    private $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    public function handleCleaningServiceCreation($formData, $username) {
        return $this->entity->createCleaningService($formData, $username);
    }
}

/** BOUNDARY LAYER */
class CleaningServicePage {
    private $message;
    private $controller;

    public function __construct($controller, $message = "") {
        $this->controller = $controller;
        $this->message = $message;
    }

    public function processRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Debug: Log the received POST data
            error_log("Received POST data: " . print_r($_POST, true));
            $this->handleFormSubmission($_POST);
        }
    }

    public function handleFormSubmission($formData) {
        $username = $_SESSION['username'];

        try {
            // Validate required fields
            $requiredFields = ['service_title', 'service_description', 'service_type', 'service_price'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($formData[$field]) || trim($formData[$field]) === '') {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                $this->message = "Please fill in all required fields";
                return;
            }

            // Validate price is a positive number
            if (!is_numeric($formData['service_price']) || $formData['service_price'] <= 0) {
                $this->message = "Service price must be a positive number.";
                return;
            }

            $isCreated = $this->controller->handleCleaningServiceCreation($formData, $username);

            // Determine message
            if ($isCreated) {
                $this->message = "Cleaning service created successfully!";
            } else {
                $this->message = "Failed to create cleaning service.";
            }
        } catch (Exception $e) {
            error_log("Error creating cleaning service: " . $e->getMessage());
            $this->message = "Error: " . $e->getMessage();
        }
    }

    public function display() {
        ?>
        <html>
        <head>
            <title>Cleaning Service Creation Page</title>
            <style>
                /* General styling for the page */
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    margin: 0;
                    padding: 0;
                }
                
                /* Form container */
                .form-container {
                    max-width: 600px;
                    margin: 50px auto;
                    padding: 30px;
                    background-color: #ffffff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                }

                /* Form title */
                .form-container h1 {
                    font-size: 2.5em;
                    text-align: center;
                    color: #343a40;
                    margin-bottom: 20px;
                }

                /* Table styling for form inputs */
                .form-container table {
                    width: 100%;
                    border-collapse: collapse;
                }

                /* Table cells */
                .form-container td {
                    padding: 10px;
                }

                /* Labels */
                .form-container label {
                    font-size: 1.1em;
                    color: #343a40;
                    font-weight: bold;
                }

                /* Input fields and textarea */
                .form-container input[type="text"],
                .form-container input[type="number"],
                .form-container textarea {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    font-size: 1em;
                }

                /* Textarea styling */
                .form-container textarea {
                    resize: vertical;
                    height: 100px;
                }

                /* Buttons styling */
                .form-container button,
                .form-container input[type="submit"] {
                    background-color: #007bff;
                    color: white;
                    font-size: 1em;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .form-container button:hover,
                .form-container input[type="submit"]:hover {
                    background-color: #0056b3;
                }

                /* 'Return' button styling */
                .form-container button[type="button"] {
                    background-color: #6c757d;
                }

                .form-container button[type="button"]:hover {
                    background-color: #5a6268;
                }

                /* Center-align buttons */
                .form-container .button-container {
                    text-align: center;
                    margin-top: 20px;
                }

                /* Message styling */
                .message {
                    text-align: center;
                    padding: 10px;
                    margin: 10px 0;
                    border-radius: 5px;
                }

                .error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                /* Service type container */
                .service-type-container {
                    position: relative;
                    display: flex;
                    align-items: center;
                }

                .help-icon {
                    margin-left: 10px;
                    cursor: help;
                    color: #007bff;
                    font-size: 20px;
                }

                .tooltip {
                    display: none;
                    position: absolute;
                    right: -300px;
                    top: 0;
                    width: 280px;
                    padding: 10px;
                    background: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    z-index: 1000;
                }

                .help-icon:hover + .tooltip {
                    display: block;
                }

                .tooltip ul {
                    margin: 0;
                    padding-left: 20px;
                }

                .tooltip li {
                    margin: 5px 0;
                }

                .tooltip-title {
                    font-weight: bold;
                    margin-bottom: 5px;
                }
            </style>
        </head>
        <body>
            <div class="form-container">
                <h1>Create Cleaning Service</h1>

                <?php if (!empty($this->message)): ?>
                    <div class="message <?php echo strpos($this->message, 'successfully') !== false ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($this->message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <table>
                        <tr>
                            <td><label for="service_title">Service Title:</label></td>
                            <td><input type="text" id="service_title" name="service_title" required/></td>
                        </tr>
                        <tr>
                            <td><label for="service_description">Service Description:</label></td>
                            <td><textarea id="service_description" name="service_description" required></textarea></td>
                        </tr>
                        <tr>
                            <td><label for="service_type">Service Type:</label></td>
                            <td>
                                <div class="service-type-container">
                                    <input type="text" id="service_type" name="service_type" required/>
                                    <span class="help-icon">?</span>
                                    <div class="tooltip">
                                        <div class="tooltip-title">Example Service Types:</div>
                                        <ul>
                                            <li>Regular Cleaning</li>
                                            <li>Deep Cleaning</li>
                                            <li>Move In/Out Cleaning</li>
                                            <li>Window Cleaning</li>
                                            <li>Carpet Cleaning</li>
                                        </ul>
                                        <div class="tooltip-title">Note:</div>
                                        <ul>
                                            <li>You can also enter your own service type if it's not listed here.</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="service_price">Service Price:</label></td>
                            <td><input type="number" id="service_price" name="service_price" step="0.01" min="0" required/></td>
                        </tr>
                    </table>
                    <div class="button-container">
                        <button type="button" onclick="window.location.href='cleaner_view_listings.php'">Return</button>
                        <button type="submit" name="submit">Create Service</button>
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

/** MAIN LOGIC */
$cleaningServiceEntity = new CleaningService();
$cleaningServiceController = new CleaningServiceController($cleaningServiceEntity);
$cleaningServicePage = new CleaningServicePage($cleaningServiceController);

// Process the form submission and display the UI
$cleaningServicePage->processRequest();
$cleaningServicePage->display();
?>