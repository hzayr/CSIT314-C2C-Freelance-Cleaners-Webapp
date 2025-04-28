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

    public function deleteService($service_id, $username) {
        // First verify that this service belongs to the logged-in user
        $stmt = $this->db->prepare("
            SELECT 1 FROM cleaningservices cs
            JOIN users u ON cs.cleaner_id = u.user_id
            WHERE cs.service_id = ? AND u.username = ?
        ");
        $stmt->execute([$service_id, $username]);
        if (!$stmt->fetch()) {
            throw new Exception("Service not found or access denied.");
        }

        // Delete the service
        $stmt = $this->db->prepare("DELETE FROM cleaningservices WHERE service_id = ?");
        return $stmt->execute([$service_id]);
    }
}

/** CONTROLLER CLASS */
class DeleteServiceController {
    private $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    public function deleteService($service_id, $username) {
        return $this->entity->deleteService($service_id, $username);
    }
}

// Main Script
if (isset($_GET['service_id'])) {
    $cleaningServiceEntity = new CleaningService();
    $deleteServiceController = new DeleteServiceController($cleaningServiceEntity);

    try {
        if ($deleteServiceController->deleteService($_GET['service_id'], $_SESSION['username'])) {
            $_SESSION['success_message'] = "Service deleted successfully.";
            header("Location: cleaner_view_listings.php");
            exit();
        } else {
            throw new Exception("Failed to delete service.");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: cleaner_view_listings.php");
        exit();
    }
} else {
    header("Location: cleaner_view_listings.php");
    exit();
}
?> 