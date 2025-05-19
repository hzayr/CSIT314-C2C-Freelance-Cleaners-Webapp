<?php
session_start();
require_once "../connectDatabase.php";

// Entity Layer
class ReviewEntity {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function verifyMatch($match_id, $homeowner_id) {
        $stmt = $this->conn->prepare("
            SELECT 1 FROM matches 
            WHERE match_id = ? AND homeowner_id = ? AND status = 'accepted'
        ");
        $stmt->bind_param("ii", $match_id, $homeowner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function updateReview($match_id, $rating, $review) {
        $stmt = $this->conn->prepare("
            UPDATE matches 
            SET rating = ?, review = ? 
            WHERE match_id = ?
        ");
        $stmt->bind_param("isi", $rating, $review, $match_id);
        return $stmt->execute();
    }
}

// Control Layer
class ReviewController {
    private $reviewEntity;

    public function __construct($reviewEntity) {
        $this->reviewEntity = $reviewEntity;
    }

    public function addReview($match_id, $rating, $review, $homeowner_id) {
        // Validate rating
        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Invalid rating'];
        }

        // Verify match belongs to homeowner
        if (!$this->reviewEntity->verifyMatch($match_id, $homeowner_id)) {
            return ['success' => false, 'message' => 'Match not found or unauthorized'];
        }

        // Update review
        if ($this->reviewEntity->updateReview($match_id, $rating, $review)) {
            return ['success' => true, 'message' => 'Review submitted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to save review'];
        }
    }
}

// Boundary Layer
class ReviewBoundary {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function handleRequest() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        // Check if required parameters are present
        if (!isset($_POST['match_id']) || !isset($_POST['rating']) || !isset($_POST['review'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            return;
        }

        $result = $this->controller->addReview(
            $_POST['match_id'],
            $_POST['rating'],
            $_POST['review'],
            $_SESSION['user_id']
        );

        echo json_encode($result);
    }
}

// Main execution
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $reviewEntity = new ReviewEntity($conn);
    $reviewController = new ReviewController($reviewEntity);
    $reviewBoundary = new ReviewBoundary($reviewController);
    
    $reviewBoundary->handleRequest();
    
    $database->closeConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 