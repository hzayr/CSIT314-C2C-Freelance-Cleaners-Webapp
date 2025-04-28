<?php
session_start();
require_once "../connectDatabase.php";

// Ensure all output is JSON
header('Content-Type: application/json');

// Error handler to catch any PHP errors and return them as JSON
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'message' => 'Server error occurred.',
        'debug' => "$errstr in $errfile on line $errline"
    ];
    echo json_encode($error);
    exit;
}
set_error_handler('handleError');

try {
    // Entity: Represents a shortlist entry and manages shortlist-related operations
    class Shortlist {
        private $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        public function addToShortlist($service_id, $user_id) {
            try {
                // First check if this service is already in the user's shortlist
                if ($this->isServiceShortlisted($service_id, $user_id)) {
                    return ['success' => false, 'message' => 'Service is already in your shortlist.'];
                }

                // Add to shortlist
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

        public function getServiceDetails($service_id) {
            $stmt = $this->conn->prepare("
                SELECT 
                    cs.service_id,
                    cs.service_title,
                    cs.service_type,
                    cs.service_price,
                    cs.service_description,
                    p.first_name,
                    p.last_name
                FROM cleaningservices cs
                JOIN profile p ON cs.cleaner_id = p.user_id
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

    // Controller: Manages the shortlist operations
    class AddToShortlistController {
        private $shortlist;

        public function __construct($shortlist) {
            $this->shortlist = $shortlist;
        }

        public function addToShortlist($service_id, $user_id) {
            return $this->shortlist->addToShortlist($service_id, $user_id);
        }

        public function getServiceDetails($service_id) {
            return $this->shortlist->getServiceDetails($service_id);
        }

        public function isServiceShortlisted($service_id, $user_id) {
            return $this->shortlist->isServiceShortlisted($service_id, $user_id);
        }
    }

    // Handle the request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['service_id'])) {
        $service_id = intval($_GET['service_id']);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            throw new Exception('User not logged in.');
        }

        // Initialize components
        $database = new Database();
        $conn = $database->getConnection();
        $shortlist = new Shortlist($conn);
        $controller = new AddToShortlistController($shortlist);

        $result = $controller->addToShortlist($service_id, $user_id);
        echo json_encode($result);
        exit;
    } else {
        throw new Exception('Invalid request method or missing service ID.');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

// If we get here, something went wrong
echo json_encode([
    'success' => false,
    'message' => 'Invalid request.'
]);
exit;

// Close the database connection
$database->closeConnection();
?>
