<?php
session_start();
require_once "../connectDatabase.php";

// Ensure all output is JSON
header('Content-Type: application/json');

// Entity Layer
class ShortlistEntity {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function isServiceShortlisted($service_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count 
                FROM shortlist 
                WHERE service_id = ? AND user_id = ?
            ");
            if (!$stmt) {
                throw new Exception("Database error: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $service_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['count'] > 0;
        } catch (Exception $e) {
            throw new Exception("Error checking shortlist: " . $e->getMessage());
        }
    }

    public function addToShortlist($service_id, $user_id) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO shortlist (service_id, user_id, shortlist_date) 
                VALUES (?, ?, NOW())
            ");
            if (!$stmt) {
                throw new Exception("Database error: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $service_id, $user_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                return ['success' => true, 'message' => 'Service added to shortlist successfully.'];
            } else {
                throw new Exception("Failed to add service to shortlist.");
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getServiceDetails($service_id) {
        $stmt = $this->conn->prepare("
            SELECT cs.service_id, cs.service_title, sc.category as service_category, cs.service_price,
            cs.service_description, cs.views, u.username, p.first_name, p.last_name
            FROM cleaningservices cs
            JOIN users u ON cs.cleaner_id = u.user_id
            JOIN profile p ON u.user_id = p.user_id
            JOIN service_categories sc ON cs.service_category = sc.category_id
            WHERE cs.service_id = ?
        ");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();
        $stmt->close();
        return $service;
    }
}

// Control Layer
class ShortlistController {
    private $shortlistEntity;

    public function __construct($shortlistEntity) {
        $this->shortlistEntity = $shortlistEntity;
    }

    public function addToShortlist($service_id, $user_id) {
        if ($this->shortlistEntity->isServiceShortlisted($service_id, $user_id)) {
            return ['success' => false, 'message' => 'Service is already in your shortlist.'];
        }
        return $this->shortlistEntity->addToShortlist($service_id, $user_id);
    }

    public function getServiceDetails($service_id) {
        return $this->shortlistEntity->getServiceDetails($service_id);
    }
}

// Boundary Layer
class ShortlistBoundary {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['service_id'])) {
                throw new Exception('Invalid request method or missing service ID.');
            }

            $service_id = intval($_GET['service_id']);
            $user_id = $_SESSION['user_id'] ?? null;

            if (!$user_id) {
                throw new Exception('User not logged in.');
            }

            $result = $this->controller->addToShortlist($service_id, $user_id);
            echo json_encode($result);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}

// Main execution
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $shortlistEntity = new ShortlistEntity($conn);
    $shortlistController = new ShortlistController($shortlistEntity);
    $shortlistBoundary = new ShortlistBoundary($shortlistController);
    
    $shortlistBoundary->handleRequest();
    
    $database->closeConnection();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
