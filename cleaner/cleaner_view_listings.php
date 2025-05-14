<?php
require "../connectDatabase.php";
session_start();

// Entity Layer: Handles database operations for cleaning services
class CleaningService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getServicesByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM cleaningservices WHERE cleaner_id = (SELECT user_id FROM users WHERE username = ?)");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchCleaningService($username, $role, $search)
    {
        $query = "SELECT * FROM cleaningservices WHERE cleaner_id = (SELECT user_id FROM users WHERE username = ?)";
        if ($search) {
            $query .= " AND service_title LIKE ?";
        }
        $stmt = $this->conn->prepare($query);
        if ($search) {
            $search = "%$search%";
            $stmt->bind_param("ss", $username, $search);
        } else {
            $stmt->bind_param("s", $username);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// Entity Layer: Handles database operations for shortlist
class ShortlistEntity
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getShortlistedUsers($service_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM shortlist WHERE service_id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// Control Layer: Manages cleaning service operations
class SearchCleaningServiceController
{
    private $cleaningService;
    private $username;

    public function __construct($cleaningService)
    {
        $this->cleaningService = $cleaningService;
        $this->username = $_SESSION['username'];
    }

    public function getServices()
    {
        return $this->cleaningService->getServicesByUsername($this->username);
    }

    public function searchServices($role, $search)
    {
        return $this->cleaningService->searchCleaningService($this->username, $role, $search);
    }
}

// Control Layer: Manages shortlist operations
class ShortlistController
{
    private $shortlistEntity;

    public function __construct($shortlistEntity)
    {
        $this->shortlistEntity = $shortlistEntity;
    }

    public function getShortlistedUsers($service_id)
    {
        return $this->shortlistEntity->getShortlistedUsers($service_id);
    }
}

// Boundary Layer: Handles UI and form submission
class SearchCleaningServicePage
{
    private $controller;
    private $shortlistController;

    public function __construct($controller, $shortlistController)
    {
        $this->controller = $controller;
        $this->shortlistController = $shortlistController;
    }

    public function display()
    {
        // Your existing display logic here
    }

    public function handleFormSubmission()
    {
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        if (isset($_POST['searchButton'])) {
            $role = strtolower($_POST['service']);
            $search = $_POST['search'];
            return $this->controller->searchServices($role, $search);
        }

        if (isset($_POST['create'])) {
            header("Location: cleaner_create_listings.php");
            exit();
        }

        if (isset($_POST['view'])) {
            $service_id = $_POST['service_id'];
            header("Location: cleaner_service_details.php?service_id=" . urlencode($service_id));
            exit();
        }

        return $this->controller->getServices();
    }
}

// Initialize and run the application
$conn = new mysqli("localhost", "root", "", "csit314");
$cleaningService = new CleaningService($conn);
$shortlistEntity = new ShortlistEntity($conn);
$controller = new SearchCleaningServiceController($cleaningService);
$shortlistController = new ShortlistController($shortlistEntity);
$page = new SearchCleaningServicePage($controller, $shortlistController);
$page->display();
?>
