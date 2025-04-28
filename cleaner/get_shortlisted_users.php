<?php
require "../connectDatabase.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if service_id is provided
if (!isset($_GET['service_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Service ID is required']);
    exit();
}

$service_id = $_GET['service_id'];

// Create controller and get shortlisted users
$shortlistController = new ShortlistController();
$shortlistedUsers = $shortlistController->getShortlistedUsers($service_id);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($shortlistedUsers);
?> 