<?php
session_start();
require_once "../connectDatabase.php";

// Entity: ServiceCategory handles database operations
class ServiceCategory {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategory($category_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                sc.category_id,
                sc.category,
                sc.status_id,
                COUNT(cs.service_category) as service_count
            FROM 
                service_categories sc
            LEFT JOIN 
                cleaningservices cs ON sc.category_id = cs.service_category
            WHERE 
                sc.category_id = ?
            GROUP BY 
                sc.category_id, sc.category, sc.status_id
        ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $result['status'] = $result['status_id'] == 1 ? 'Active' : 'Suspended';
        }
        return $result;
    }

    public function suspendCategory($category_id) {
        $stmt = $this->conn->prepare("UPDATE service_categories SET status_id = 2 WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        return $stmt->execute();
    }

    public function unsuspendCategory($category_id) {
        $stmt = $this->conn->prepare("UPDATE service_categories SET status_id = 1 WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        return $stmt->execute();
    }
}

// Controller: Manages the category operations
class ServiceCategoryController {
    private $category;

    public function __construct($category) {
        $this->category = $category;
    }

    public function getCategory($category_id) {
        return $this->category->getCategory($category_id);
    }

    public function suspendCategory($category_id) {
        return $this->category->suspendCategory($category_id);
    }

    public function unsuspendCategory($category_id) {
        return $this->category->unsuspendCategory($category_id);
    }
}

// Boundary: Manages the display of data
class SuspendCategoryPage {
    private $controller;
    private $categoryData;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'];
            $action = $_POST['action'];

            try {
                if ($action === 'suspend') {
                    if ($this->controller->suspendCategory($category_id)) {
                        $_SESSION['success_message'] = "Category suspended successfully.";
                    } else {
                        throw new Exception("Failed to suspend category.");
                    }
                } elseif ($action === 'unsuspend') {
                    if ($this->controller->unsuspendCategory($category_id)) {
                        $_SESSION['success_message'] = "Category suspension removed.";
                    } else {
                        throw new Exception("Failed to remove suspension.");
                    }
                }
                header("Location: pm_view_service_categories.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
            }
        }

        $category_id = $_GET['id'] ?? '';
        if ($category_id) {
            $this->categoryData = $this->controller->getCategory($category_id);
            $this->displayForm();
        } else {
            echo "No category provided.";
        }
    }

    public function displayForm() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Suspend Category - clean.sg</title>
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
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-bottom: 20px;
                }

                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                .info-table th, .info-table td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #dee2e6;
                }

                .info-table th {
                    background-color: #f8f9fa;
                    color: #343a40;
                    font-weight: bold;
                    width: 30%;
                }

                .button-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 10px;
                    margin-top: 20px;
                }

                .suspend-btn, .unsuspend-btn, .return-btn {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 1em;
                    text-align: center;
                    text-decoration: none;
                    color: white;
                    min-width: 300px;
                    width: 300px;
                    box-sizing: border-box;
                }

                .suspend-btn {
                    background-color: #ff6b35;
                }

                .suspend-btn:hover {
                    background-color: #ff8c5a;
                }

                .unsuspend-btn {
                    background-color: #28a745;
                }

                .unsuspend-btn:hover {
                    background-color: #218838;
                }

                .return-btn {
                    background-color: #6c757d;
                }

                .return-btn:hover {
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
                <h2>Category Suspension</h2>
                
                <table class="info-table">
                    <tr>
                        <th>Category Name</th>
                        <td><?php echo htmlspecialchars($this->categoryData['category']); ?></td>
                    </tr>
                    <tr>
                        <th>Number of Services</th>
                        <td><?php echo htmlspecialchars($this->categoryData['service_count']); ?></td>
                    </tr>
                    <tr>
                        <th>Current Status</th>
                        <td><?php echo htmlspecialchars($this->categoryData['status'] ?? 'Active'); ?></td>
                    </tr>
                </table>

                <div class="button-container">
                    <?php if ($this->categoryData['status_id'] == 1): ?>
                        <form action="" method="POST">
                            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($this->categoryData['category_id']); ?>">
                            <input type="hidden" name="action" value="suspend">
                            <button type="submit" class="suspend-btn">Suspend Category</button>
                        </form>
                    <?php else: ?>
                        <form action="" method="POST">
                            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($this->categoryData['category_id']); ?>">
                            <input type="hidden" name="action" value="unsuspend">
                            <button type="submit" class="unsuspend-btn">Remove Suspension</button>
                        </form>
                    <?php endif; ?>
                    <a href="pm_view_service_categories.php" class="return-btn">Return to Categories</a>
                </div>
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
$page = new SuspendCategoryPage($controller);

$page->handleRequest();
?>
