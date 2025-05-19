<?php
session_start();
require_once "../connectDatabase.php";

// Entity: ServiceCategory handles database operations
class ServiceCategory {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllCategories() {
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
            GROUP BY 
                sc.category_id, sc.category, sc.status_id
            ORDER BY 
                sc.category ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $row['status'] = $row['status_id'] == 1 ? 'Active' : 'Suspended';
            $categories[] = $row;
        }
        return $categories;
    }
}

// Controller: Manages the category operations
class ServiceCategoryController {
    private $category;

    public function __construct($category) {
        $this->category = $category;
    }

    public function getAllCategories() {
        return $this->category->getAllCategories();
    }
}

// Boundary: Manages the display of data
class ServiceCategoryPage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function displayCategories() {
        $categories = $this->controller->getAllCategories();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Service Categories - clean.sg</title>
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

                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-top: 20px;
                }

                table {
                    width: 90%;
                    margin: 20px auto;
                    border-collapse: collapse;
                    background-color: white;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                table, th, td {
                    border: 1px solid #dee2e6;
                }

                th, td {
                    padding: 12px;
                    text-align: center;
                    color: #343a40;
                }

                th {
                    background-color: #6c757d;
                    color: #ffffff;
                    font-weight: bold;
                }

                tr:nth-child(even) {
                    background-color: #f1f1f1;
                }

                .back-link {
                    display: block;
                    text-align: center;
                    margin: 20px 0;
                }

                .back-link a {
                    color: #007bff;
                    text-decoration: none;
                }

                .back-link a:hover {
                    text-decoration: underline;
                }

                .action-buttons {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }

                .update-btn, .suspend-btn {
                    padding: 6px 12px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 0.9em;
                    text-decoration: none;
                    color: white;
                }

                .update-btn {
                    background-color: #28a745;
                }

                .update-btn:hover {
                    background-color: #218838;
                }

                .suspend-btn {
                    background-color: #ff6b35;  /* Orange-red color */
                    color: white;
                    text-decoration: none;
                    padding: 6px 12px;
                    border-radius: 4px;
                    font-size: 0.9em;
                }

                .suspend-btn:hover {
                    background-color: #ff8c5a;  /* Lighter orange-red for hover */
                }

                .delete-btn {
                    background-color: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    padding: 6px 12px;
                    cursor: pointer;
                    font-size: 0.9em;
                }

                .delete-btn:hover {
                    background-color: #c82333;
                }

                /* Popup Styles */
                .popup-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                }

                .popup-content {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    z-index: 1001;
                }

                .popup-message {
                    margin-bottom: 20px;
                    font-size: 18px;
                    color: #333;
                }

                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }

                .popup-confirm {
                    background-color: #dc3545;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .popup-cancel {
                    background-color: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>

            <div style="display: flex; justify-content: center; align-items: center; gap: 20px;">
                <h2>Service Categories</h2>
                <a href="pm_add_service_category.php" style="background-color: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-size: 0.9em;">+ Add</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Number of Services</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['category']); ?></td>
                                <td><?php echo htmlspecialchars($category['service_count']); ?></td>
                                <td><?php echo htmlspecialchars($category['status']); ?></td>
                                <td class="action-buttons">
                                    <a href="pm_update_category.php?id=<?php echo $category['category_id']; ?>" class="update-btn">Update</a>
                                    <a href="pm_suspend_category.php?id=<?php echo $category['category_id']; ?>" class="suspend-btn">Suspend</a>
                                    <form action="pm_delete_category.php" method="get" class="delete-form" style="display: inline;">
                                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                        <button type="button" class="delete-btn" onclick="showDeletePopup(this)">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="back-link">
                <a href="pm_dashboard.php">← Back to Dashboard</a>
            </div>

            <!-- Add popup HTML -->
            <div id="deletePopup" class="popup-overlay">
                <div class="popup-content">
                    <div class="popup-message">Are you sure you want to delete this category?</div>
                    <div class="popup-buttons">
                        <button id="confirmDelete" class="popup-confirm">Delete</button>
                        <button id="cancelDelete" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <script>
                function showDeletePopup(button) {
                    const popup = document.getElementById('deletePopup');
                    const form = button.closest('form');
                    
                    popup.style.display = 'block';

                    document.getElementById('confirmDelete').onclick = function() {
                        form.submit();
                    };

                    document.getElementById('cancelDelete').onclick = function() {
                        popup.style.display = 'none';
                    };

                    // Close popup when clicking outside
                    popup.onclick = function(e) {
                        if (e.target === popup) {
                            popup.style.display = 'none';
                        }
                    };
                }
            </script>
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
$page = new ServiceCategoryPage($controller);

$page->displayCategories();
?>
