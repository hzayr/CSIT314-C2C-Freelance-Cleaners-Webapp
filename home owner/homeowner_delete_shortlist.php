<?php
require_once '../connectDatabase.php';
session_start();

// Set JSON header for AJAX responses
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

if (!isset($_GET['shortlist_id'])) {
    echo json_encode(['success' => false, 'message' => 'Shortlist ID not provided.']);
    exit();
}

// ENTITY LAYER: Manages database interactions
class Shortlist
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function deleteShortlist($shortlist_id, $user_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM shortlist WHERE shortlist_id = ? AND user_id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $this->db->error);
            }

            $stmt->bind_param("ii", $shortlist_id, $user_id);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return ['success' => true, 'message' => 'Service removed from shortlist successfully.'];
            } else {
                throw new Exception("Failed to remove service from shortlist.");
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// CONTROLLER LAYER: Interacts with the Entity layer
class DeleteShortlistController
{
    private $shortlist;

    public function __construct($shortlist)
    {
        $this->shortlist = $shortlist;
    }

    public function deleteShortlist($shortlist_id, $user_id)
    {
        return $this->shortlist->deleteShortlist($shortlist_id, $user_id);
    }
}

try {
    // Process deletion request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['shortlist_id']) && isset($_POST['confirm_delete'])) {
        $shortlist_id = intval($_GET['shortlist_id']);
        $user_id = $_SESSION['user_id'];

        $db = new Database();
        $shortlist = new Shortlist($db->getConnection());
        $controller = new DeleteShortlistController($shortlist);

        $result = $controller->deleteShortlist($shortlist_id, $user_id);
        echo json_encode($result);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}
