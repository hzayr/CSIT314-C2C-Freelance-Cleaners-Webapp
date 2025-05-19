<?php
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

/** ENTITY CLASS */
class CleaningService {
    private $db;

    public function __construct() {
        try {
            $servername = getenv('DB_HOST') ?: "127.0.0.1";
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

    public function getService($service_id, $username) {
        $stmt = $this->db->prepare("
            SELECT cs.* 
            FROM cleaningservices cs
            JOIN users u ON cs.cleaner_id = u.user_id
            WHERE cs.service_id = ? AND u.username = ?
        ");
        $stmt->execute([$service_id, $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateService($service_id, $formData, $username) {
        // First get the cleaner_id from the users table
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception("User not found.");
        }
        
        $cleaner_id = $user['user_id'];

        // Verify ownership
        $stmt = $this->db->prepare("
            SELECT 1 FROM cleaningservices cs
            JOIN users u ON cs.cleaner_id = u.user_id
            WHERE cs.service_id = ? AND u.username = ?
        ");
        $stmt->execute([$service_id, $username]);
        if (!$stmt->fetch()) {
            throw new Exception("Service not found or access denied.");
        }

        // Update the service
        $stmt = $this->db->prepare("
            UPDATE cleaningservices 
            SET service_title = ?, service_description = ?, service_category = ?, service_price = ?
            WHERE service_id = ? AND cleaner_id = ?
        ");
        
        return $stmt->execute([
            $formData['service_title'],
            $formData['service_description'],
            $formData['service_category'],
            $formData['service_price'],
            $service_id,
            $cleaner_id
        ]);
    }

    public function getCategories() {
        $stmt = $this->db->prepare("SELECT category_id, category FROM service_categories ORDER BY category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/** CONTROLLER CLASS */
class UpdateServiceController {
    private $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    public function getService($service_id, $username) {
        return $this->entity->getService($service_id, $username);
    }

    public function updateService($service_id, $formData, $username) {
        return $this->entity->updateService($service_id, $formData, $username);
    }

    public function getCategories() {
        return $this->entity->getCategories();
    }
}

/** BOUNDARY CLASS */
class UpdateServicePage {
    private $controller;
    private $message;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->message = "";
    }

    public function processRequest() {
        if (!isset($_GET['service_id'])) {
            $this->message = "No service ID provided.";
            return null;
        }

        $service_id = $_GET['service_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            return $this->handleFormSubmission($_POST, $service_id);
        }

        return $this->controller->getService($service_id, $_SESSION['username']);
    }

    public function handleFormSubmission($formData, $service_id) {
        try {
            // Validate required fields
            $requiredFields = ['service_title', 'service_description', 'service_category', 'service_price'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($formData[$field]) || trim($formData[$field]) === '') {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                $this->message = "Please fill in: " . implode(', ', $missingFields);
                return $this->controller->getService($service_id, $_SESSION['username']);
            }

            // Validate price is a positive number
            if (!is_numeric($formData['service_price']) || $formData['service_price'] <= 0) {
                $this->message = "Service price must be a positive number.";
                return $this->controller->getService($service_id, $_SESSION['username']);
            }

            if ($this->controller->updateService($service_id, $formData, $_SESSION['username'])) {
                header("Location: cleaner_service_details.php?service_id=" . urlencode($service_id));
                exit();
            } else {
                $this->message = "Failed to update service.";
            }
        } catch (Exception $e) {
            $this->message = "Error: " . $e->getMessage();
        }

        return $this->controller->getService($service_id, $_SESSION['username']);
    }

    public function displayUpdateForm($service) {
        if (!$service) {
            $this->message = "Service not found or access denied.";
        }
        $categories = $this->controller->getCategories();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Update Service</title>
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
                .form-container button {
                    background-color: #007bff;
                    color: white;
                    font-size: 1em;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .form-container button:hover {
                    background-color: #0056b3;
                }

                /* 'Cancel' button styling */
                .form-container .cancel-button {
                    background-color: #dc3545;
                }

                .form-container .cancel-button:hover {
                    background-color: #c82333;
                }

                /* Center-align buttons */
                .form-container .button-group {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
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
                }

                .help-icon {
                    position: absolute;
                    right: -30px;
                    top: 8px;
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

                /* Radio button styling */
                .radio-option {
                    margin: 8px 0;
                }

                .radio-option input[type="radio"] {
                    margin-right: 8px;
                }

                .radio-option label {
                    font-weight: normal;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <div class="form-container">
                <h1>Update Service</h1>

                <?php if (!empty($this->message)): ?>
                    <div class="message error">
                        <?php echo htmlspecialchars($this->message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($service): ?>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?service_id=' . urlencode($service['service_id'])); ?>">
                        <table>
                            <tr>
                                <td><label for="service_title">Service Title:</label></td>
                                <td><input type="text" id="service_title" name="service_title" value="<?php echo htmlspecialchars($service['service_title']); ?>" required></td>
                            </tr>
                            <tr>
                                <td><label for="service_description">Service Description:</label></td>
                                <td><textarea id="service_description" name="service_description" required><?php echo htmlspecialchars($service['service_description']); ?></textarea></td>
                            </tr>
                            <tr>
                                <td><label for="service_category">Category:</label></td>
                                <td>
                                    <?php
                                    foreach ($categories as $category) {
                                        $checked = ($category['category_id'] == $service['service_category']) ? 'checked' : '';
                                        echo "<div class='radio-option'>";
                                        echo "<input type='radio' id='category_" . $category['category_id'] . "' name='service_category' value='" . $category['category_id'] . "' " . $checked . " required>";
                                        echo "<label for='category_" . $category['category_id'] . "'>" . htmlspecialchars($category['category']) . "</label>";
                                        echo "</div>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="service_price">Service Price:</label></td>
                                <td><input type="number" id="service_price" name="service_price" step="0.01" min="0" value="<?php echo htmlspecialchars($service['service_price']); ?>" required></td>
                            </tr>
                        </table>
                        <div class="button-group">
                            <button type="submit" class="button">Update Service</button>
                        </div>
                        <div class="button-group" style="margin-top: 1rem;">
                            <a href="cleaner_service_details.php?service_id=<?php echo urlencode($service['service_id']); ?>" class="button back-button">Return to Service Details</a>
                            <a href="cleaner_view_listings.php" class="button back-button">Return to My Services</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$cleaningServiceEntity = new CleaningService();
$updateServiceController = new UpdateServiceController($cleaningServiceEntity);
$updateServicePage = new UpdateServicePage($updateServiceController);

$service = $updateServicePage->processRequest();
$updateServicePage->displayUpdateForm($service);
?>
