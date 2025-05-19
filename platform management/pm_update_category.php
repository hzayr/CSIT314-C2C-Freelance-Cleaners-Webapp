<?php
session_start();
require_once "../connectDatabase.php";

// Entity: ServiceCategory handles database operations
class ServiceCategory {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategoryById($categoryId) {
        $stmt = $this->conn->prepare("SELECT * FROM service_categories WHERE category_id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateCategory($categoryId, $categoryName) {
        $stmt = $this->conn->prepare("UPDATE service_categories SET category = ? WHERE category_id = ?");
        $stmt->bind_param("si", $categoryName, $categoryId);
        return $stmt->execute();
    }
}

// Controller: Manages the category operations
class ServiceCategoryController {
    private $category;

    public function __construct($category) {
        $this->category = $category;
    }

    public function getCategoryById($categoryId) {
        return $this->category->getCategoryById($categoryId);
    }

    public function updateCategory($categoryId, $categoryName) {
        return $this->category->updateCategory($categoryId, $categoryName);
    }
}

// Boundary: Manages the display of data
class ServiceCategoryPage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function displayUpdateForm($categoryId) {
        $category = $this->controller->getCategoryById($categoryId);
        
        if (!$category) {
            echo "Category not found";
            return;
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Update Category - clean.sg</title>
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

                .container {
                    max-width: 800px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                h2 {
                    color: #343a40;
                    margin-bottom: 20px;
                    text-align: center;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #495057;
                    font-weight: bold;
                }

                input[type="text"] {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                    font-size: 1em;
                }

                .button-group {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                    margin-top: 20px;
                }

                .update-btn, .cancel-btn {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 1em;
                    text-decoration: none;
                    color: white;
                }

                .update-btn {
                    background-color: #28a745;
                }

                .update-btn:hover {
                    background-color: #218838;
                }

                .cancel-btn {
                    background-color: #6c757d;
                }

                .cancel-btn:hover {
                    background-color: #5a6268;
                }

                .back-link {
                    display: block;
                    text-align: center;
                    margin-top: 20px;
                }

                .back-link a {
                    color: #007bff;
                    text-decoration: none;
                }

                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <div class="container">
                <h2>Update Category</h2>
                <form action="pm_update_category.php" method="POST">
                    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['category_id']); ?>">
                    
                    <div class="form-group">
                        <label for="category">Category Name:</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category['category']); ?>" required>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="update-btn">Update Category</button>
                        <a href="pm_view_service_categories.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>

                <div class="back-link">
                    <a href="pm_view_service_categories.php">← Back to Categories</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $serviceCategory = new ServiceCategory($conn);
    $controller = new ServiceCategoryController($serviceCategory);

    $categoryId = $_POST['category_id'];
    $categoryName = $_POST['category'];

    if ($controller->updateCategory($categoryId, $categoryName)) {
        header("Location: pm_view_service_categories.php");
        exit();
    } else {
        echo "Error updating category";
    }
} else {
    $database = new Database();
    $conn = $database->getConnection();

    $serviceCategory = new ServiceCategory($conn);
    $controller = new ServiceCategoryController($serviceCategory);
    $page = new ServiceCategoryPage($controller);

    $categoryId = $_GET['id'] ?? null;
    if ($categoryId) {
        $page->displayUpdateForm($categoryId);
    } else {
        echo "Category ID not provided";
    }
}
?>
