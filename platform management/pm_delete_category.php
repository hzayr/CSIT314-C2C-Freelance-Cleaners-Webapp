<?php
session_start();
require_once "../connectDatabase.php";

// Entity: ServiceCategory handles database operations
class ServiceCategory {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function deleteCategory($category_id) {
        // First check if the category is being used in any services
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM cleaningservices WHERE service_category = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Cannot delete category as it is being used by existing services.");
        }

        // If not in use, proceed with deletion
        $stmt = $this->conn->prepare("DELETE FROM service_categories WHERE category_id = ?");
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

    public function deleteCategory($category_id) {
        return $this->category->deleteCategory($category_id);
    }
}

// Main Script
if (isset($_GET['category_id'])) {
    $database = new Database();
    $conn = $database->getConnection();

    $serviceCategory = new ServiceCategory($conn);
    $controller = new ServiceCategoryController($serviceCategory);

    try {
        if ($controller->deleteCategory($_GET['category_id'])) {
            $_SESSION['success_message'] = "Category deleted successfully.";
        } else {
            throw new Exception("Failed to delete category.");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

// Redirect back to the categories page
header("Location: pm_view_service_categories.php");
exit();
?> 