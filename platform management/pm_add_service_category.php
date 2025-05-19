<?php
session_start();
require_once "../connectDatabase.php";

// Entity: ServiceCategory handles database operations
class ServiceCategory {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getNextCategoryId() {
        $stmt = $this->conn->prepare("SELECT MAX(category_id) as max_id FROM service_categories");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return ($row['max_id'] ?? 0) + 1;
    }

    public function addCategory($category) {
        $nextId = $this->getNextCategoryId();
        $status_id = 1; // Default status_id for new categories
        $stmt = $this->conn->prepare("INSERT INTO service_categories (category_id, category, status_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $nextId, $category, $status_id);
        return $stmt->execute();
    }
}

// Controller: Manages the category operations
class ServiceCategoryController {
    private $category;

    public function __construct($category) {
        $this->category = $category;
    }

    public function addCategory($category) {
        return $this->category->addCategory($category);
    }
}

// Boundary: Manages the display of data
class AddServiceCategoryPage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function displayForm() {
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = trim($_POST['category']);
            
            if (empty($category)) {
                $message = '<div style="color: #dc3545; text-align: center; margin: 10px 0;">Category name cannot be empty</div>';
            } else {
                if ($this->controller->addCategory($category)) {
                    header('Location: pm_view_service_categories.php');
                    exit();
                } else {
                    $message = '<div style="color: #dc3545; text-align: center; margin: 10px 0;">Error adding category</div>';
                }
            }
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Add Service Category - clean.sg</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }

                header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    background-color: #343a40;
                    color: #ffffff;
                }

                header h1 {
                    margin: 0;
                    font-size: 1.5em;
                }

                header a {
                    text-decoration: none;
                    color: #ffffff;
                    background-color: #007bff;
                    padding: 8px 16px;
                    border-radius: 4px;
                    font-size: 0.9em;
                }

                header a[href="../logout.php"] {
                    background-color: #dc3545;
                }

                header a:hover {
                    background-color: #0056b3;
                }

                header a[href="../logout.php"]:hover {
                    background-color: #c82333;
                }

                .form-container {
                    max-width: 500px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-bottom: 20px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #343a40;
                }

                input[type="text"] {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    box-sizing: border-box;
                }

                .button-group {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }

                .submit-btn, .cancel-btn {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 0.9em;
                    text-decoration: none;
                    color: white;
                }

                .submit-btn {
                    background-color: #28a745;
                }

                .submit-btn:hover {
                    background-color: #218838;
                }

                .cancel-btn {
                    background-color: #6c757d;
                }

                .cancel-btn:hover {
                    background-color: #5a6268;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <div class="form-container">
                <h2>Add Service Category</h2>
                <?php echo $message; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="category">Category Name:</label>
                        <input type="text" id="category" name="category" required>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="submit-btn">Add Category</button>
                        <a href="pm_view_service_categories.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$database = new Database();
$conn = $database->getConnection();

$serviceCategory = new ServiceCategory($conn);
$controller = new ServiceCategoryController($serviceCategory);
$page = new AddServiceCategoryPage($controller);

$page->displayForm();
?>
