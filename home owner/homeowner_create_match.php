<?php
session_start();
require_once "../connectDatabase.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: homeowner_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['service_id']) && isset($_POST['confirm_match'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check if connection was successful
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        $service_id = $_GET['service_id'];
        $homeowner_id = $_SESSION['user_id'];

        // Log the input values for debugging
        error_log("Attempting to create match - Service ID: $service_id, Homeowner ID: $homeowner_id");

        // Get cleaner_id from the service
        $stmt = $conn->prepare("SELECT cleaner_id FROM cleaningservices WHERE service_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $service_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Get result failed: " . $stmt->error);
        }
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Service not found']);
            exit();
        }
        
        $service = $result->fetch_assoc();
        $cleaner_id = $service['cleaner_id'];

        // Check if match already exists
        $stmt = $conn->prepare("SELECT match_id FROM matches WHERE service_id = ? AND homeowner_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $service_id, $homeowner_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Get result failed: " . $stmt->error);
        }

        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'You have already matched with this service']);
            exit();
        }

        // Create new match
        $stmt = $conn->prepare("
            INSERT INTO matches (service_id, homeowner_id, cleaner_id, match_date, status, rating, review)
            VALUES (?, ?, ?, NOW(), 'pending', NULL, NULL)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("iii", $service_id, $homeowner_id, $cleaner_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        echo json_encode(['success' => true, 'message' => 'Match created successfully']);

    } catch (Exception $e) {
        error_log("Exception in homeowner_create_match.php: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    } finally {
        if (isset($database)) {
            $database->closeConnection();
        }
    }
} else {
    header("Location: homeowner_view_shortlist.php");
    exit();
}
?> 