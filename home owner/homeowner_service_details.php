<?php
session_start();
require_once "../connectDatabase.php";

// CleaningService Entity
class CleaningService {
    private $database;

    public $serviceTitle;
    public $serviceType;
    public $servicePrice;
    public $serviceDescription;
    public $cleanerFirstName;
    public $cleanerLastName;
    public $cleanerEmail;
    public $cleanerPhone;
    public $is_shortlisted;

    public function __construct($database) {
        $this->database = $database;
    }

    public function viewCleaningService($service_id) {
        $conn = $this->database->getConnection();
        $this->incrementViews($service_id, $conn);

        $buyer_id = $_SESSION['user_id'] ?? 0;
        $stmt = $conn->prepare("
            SELECT 
                cs.service_title, 
                cs.service_type, 
                cs.service_price, 
                cs.service_description, 
                p.first_name, 
                p.last_name,
                u.email,
                u.phone_num,
                CASE WHEN s.service_id IS NOT NULL THEN 1 ELSE 0 END as is_shortlisted
            FROM 
                cleaningservices cs 
            JOIN 
                profile p ON cs.cleaner_id = p.user_id 
            JOIN
                users u ON p.user_id = u.user_id
            LEFT JOIN
                shortlist s ON cs.service_id = s.service_id AND s.user_id = ?
            WHERE 
                cs.service_id = ?
        ");
        $stmt->bind_param("ii", $buyer_id, $service_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();

        if ($data) {
            $this->serviceTitle = $data['service_title'];
            $this->serviceType = $data['service_type'];
            $this->servicePrice = $data['service_price'];
            $this->serviceDescription = $data['service_description'];
            $this->cleanerFirstName = $data['first_name'];
            $this->cleanerLastName = $data['last_name'];
            $this->cleanerEmail = $data['email'];
            $this->cleanerPhone = $data['phone_num'];
            $this->is_shortlisted = (bool)$data['is_shortlisted'];
        }
        $stmt->close();

        return $this;
    }

    private function incrementViews($service_id, $conn) {
        $query = "UPDATE cleaningservices SET views = views + 1 WHERE service_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Controller
class ViewCleaningServiceController {
    private $service;

    public function __construct($service) {
        $this->service = $service;
    }

    public function viewCleaningService($service_id) {
        return $this->service->viewCleaningService($service_id);
    }
}

// View
class ViewCleaningServicePage {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function render() {
        $service_id = $_POST['service_id'] ?? $_GET['service_id'] ?? null;
        $serviceDetails = $this->controller->viewCleaningService($service_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .details p {
            margin: 10px 0;
            font-size: 1.1em;
        }

        .section-title {
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .service-section, .cleaner-section {
            margin-bottom: 20px;
            background: #f1f1f1;
            padding: 15px;
            border-radius: 6px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .common-button {
            display: inline-block;
            width: 220px;
            padding: 12px 0;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 1em;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .return-button {
            background-color: #6c757d;
        }
        .return-button:hover {
            background-color: #5a6268;
        }
        .shortlist-button {
            background-color: #28a745;
        }
        .shortlist-button:hover {
            background-color: #218838;
        }
        .shortlist-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        /* Popup */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .popup-content {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .popup-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .popup-confirm, .popup-cancel {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .popup-confirm {
            background-color: #28a745;
        }

        .popup-cancel {
            background-color: #dc3545;
        }

        /* Notification */
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
        }

        .notification.success { background: #28a745; }
        .notification.error { background: #dc3545; }
    </style>
</head>
<body>

<div id="shortlistPopup" class="popup-overlay">
    <div class="popup-content">
        <div>Are you sure you want to add this service to your shortlist?</div>
        <div class="popup-buttons">
            <button id="confirmShortlist" class="popup-confirm">Confirm</button>
            <button id="cancelShortlist" class="popup-cancel">Cancel</button>
        </div>
    </div>
</div>

<div id="notification" class="notification"></div>

<div class="container">
    <h1>Service Details</h1>
    <?php if ($serviceDetails): ?>
        <div class="details">
            <div class="service-section">
                <div class="section-title">Service Information</div>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($serviceDetails->serviceTitle); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($serviceDetails->serviceType); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($serviceDetails->servicePrice, 2); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($serviceDetails->serviceDescription); ?></p>
            </div>

            <div class="cleaner-section">
                <div class="section-title">Cleaner Information</div>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($serviceDetails->cleanerFirstName . " " . $serviceDetails->cleanerLastName); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($serviceDetails->cleanerEmail); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($serviceDetails->cleanerPhone); ?></p>
            </div>

            <div class="button-container">
                <?php
                    $referrer = $_GET['referrer'] ?? $_POST['referrer'] ?? 'dashboard';
                    $backUrl = $referrer === 'shortlist' ? 'homeowner_view_shortlist.php' : 'homeowner_dashboard.php';
                ?>
                <a href="<?php echo $backUrl; ?>" class="common-button return-button">Return to <?php echo ucfirst($referrer); ?></a>

                <?php if (!$serviceDetails->is_shortlisted): ?>
                    <button class="common-button shortlist-button" onclick="showShortlistPopup(<?php echo $service_id; ?>)">Add to Shortlist</button>
                <?php else: ?>
                    <button class="common-button shortlist-button" disabled>Already Shortlisted</button>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p>Service not found.</p>
        <div class="button-container">
            <a href="homeowner_dashboard.php" class="common-button">Return to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<script>
    let currentServiceId = null;

    function showShortlistPopup(serviceId) {
        currentServiceId = serviceId;
        document.getElementById('shortlistPopup').style.display = 'block';
    }

    document.getElementById('confirmShortlist').onclick = function() {
        if (!currentServiceId) return;

        fetch('homeowner_add_shortlist.php?service_id=' + currentServiceId, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    const button = document.querySelector(`button[onclick="showShortlistPopup(${currentServiceId})"]`);
                    if (button) {
                        button.disabled = true;
                        button.textContent = 'Already Shortlisted';
                        button.removeAttribute('onclick');
                    }
                } else {
                    showNotification(data.message || 'Failed to add.', 'error');
                }
            })
            .catch(() => showNotification('Error occurred.', 'error'))
            .finally(() => {
                document.getElementById('shortlistPopup').style.display = 'none';
                currentServiceId = null;
            });
    };

    document.getElementById('cancelShortlist').onclick = function() {
        document.getElementById('shortlistPopup').style.display = 'none';
        currentServiceId = null;
    };

    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = 'notification ' + type;
        notification.style.display = 'block';
        setTimeout(() => notification.style.display = 'none', 3000);
    }
</script>

</body>
</html>
<?php
    }
}

// Bootstrap
$database = new Database();
$service = new CleaningService($database);
$controller = new ViewCleaningServiceController($service);
$view = new ViewCleaningServicePage($controller);
$view->render();
$database->closeConnection();
?>
