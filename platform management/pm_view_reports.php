<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once "../connectDatabase.php";

// Entity Layer: Handles data access and business logic
class ReportEntity {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getMatchesByDate($date) {
        $stmt = $this->conn->prepare("SELECT m.*, u.username as homeowner_username, 
            p.first_name as homeowner_first_name, p.last_name as homeowner_last_name, 
            v.username as cleaner_username, q.first_name as cleaner_first_name, 
            q.last_name as cleaner_last_name, cs.service_title 
            FROM matches m 
            JOIN users u ON m.homeowner_id = u.user_id 
            JOIN profile p ON u.user_id = p.user_id 
            JOIN users v ON m.cleaner_id = v.user_id 
            JOIN profile q ON v.user_id = q.user_id 
            JOIN cleaningservices cs ON m.service_id = cs.service_id 
            WHERE DATE(m.match_date) = ? 
            ORDER BY m.match_date DESC");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        $stmt->close();
        return $matches;
    }

    public function getMatchesByWeek($weekStart) {
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        $stmt = $this->conn->prepare("SELECT m.*, u.username as homeowner_username, 
            p.first_name as homeowner_first_name, p.last_name as homeowner_last_name, 
            v.username as cleaner_username, q.first_name as cleaner_first_name, 
            q.last_name as cleaner_last_name, cs.service_title 
            FROM matches m 
            JOIN users u ON m.homeowner_id = u.user_id 
            JOIN profile p ON u.user_id = p.user_id 
            JOIN users v ON m.cleaner_id = v.user_id 
            JOIN profile q ON v.user_id = q.user_id 
            JOIN cleaningservices cs ON m.service_id = cs.service_id 
            WHERE DATE(m.match_date) BETWEEN ? AND ? 
            ORDER BY m.match_date DESC");
        $stmt->bind_param("ss", $weekStart, $weekEnd);
        $stmt->execute();
        $result = $stmt->get_result();
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        $stmt->close();
        return $matches;
    }

    public function getMatchesByMonth($year, $month) {
        $startDate = $year . '-' . $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        $stmt = $this->conn->prepare("SELECT m.*, u.username as homeowner_username, 
            p.first_name as homeowner_first_name, p.last_name as homeowner_last_name, 
            v.username as cleaner_username, q.first_name as cleaner_first_name, 
            q.last_name as cleaner_last_name, cs.service_title 
            FROM matches m 
            JOIN users u ON m.homeowner_id = u.user_id 
            JOIN profile p ON u.user_id = p.user_id 
            JOIN users v ON m.cleaner_id = v.user_id 
            JOIN profile q ON v.user_id = q.user_id 
            JOIN cleaningservices cs ON m.service_id = cs.service_id 
            WHERE DATE(m.match_date) BETWEEN ? AND ? 
            ORDER BY m.match_date DESC");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        $stmt->close();
        return $matches;
    }
}

// Control Layer: Handles business logic and coordinates between Entity and Boundary
class ReportController {
    private $entity;
    
    public function __construct($entity) {
        $this->entity = $entity;
    }
    
    public function getMatchesByDate($date) {
        return $this->entity->getMatchesByDate($date);
    }

    public function getMatchesByWeek($weekStart) {
        return $this->entity->getMatchesByWeek($weekStart);
    }

    public function getMatchesByMonth($year, $month) {
        return $this->entity->getMatchesByMonth($year, $month);
    }

    public function getWeekRange($date) {
        $start = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $end = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
        return [
            'start' => $start,
            'end' => $end,
            'display' => date('M d', strtotime($start)) . ' - ' . date('M d, Y', strtotime($end))
        ];
    }

    public function getCurrentDate() {
        return date('Y-m-d');
    }

    public function getCurrentWeekStart() {
        return date('Y-m-d', strtotime('monday this week'));
    }

    public function getCurrentYear() {
        return date('Y');
    }

    public function getCurrentMonth() {
        return date('m');
    }

    public function getYearOptions() {
        $currentYear = date('Y');
        $years = [];
        for($i = $currentYear; $i >= $currentYear - 2; $i--) {
            $years[] = $i;
        }
        return $years;
    }

    public function getMonthOptions() {
        $months = [];
        for($i = 1; $i <= 12; $i++) {
            $months[] = [
                'value' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'name' => date('F', mktime(0, 0, 0, $i, 1))
            ];
        }
        return $months;
    }
}

// Boundary Layer: Handles UI and user interaction
class ReportBoundary {
    private $controller;
    private $view_type;
    private $matches;
    private $weekRange;
    
    public function __construct($controller) {
        $this->controller = $controller;
        $this->initializeView();
    }
    
    private function initializeView() {
        $this->view_type = isset($_GET['view_type']) ? $_GET['view_type'] : 'day';
        $this->matches = [];
        
        switch($this->view_type) {
            case 'day':
                $selected_date = isset($_GET['date']) ? $_GET['date'] : $this->controller->getCurrentDate();
                $this->matches = $this->controller->getMatchesByDate($selected_date);
                break;
            case 'week':
                $selected_week = isset($_GET['week']) ? $_GET['week'] : $this->controller->getCurrentWeekStart();
                $this->weekRange = $this->controller->getWeekRange($selected_week);
                $this->matches = $this->controller->getMatchesByWeek($selected_week);
                break;
            case 'month':
                $selected_year = isset($_GET['year']) ? $_GET['year'] : $this->controller->getCurrentYear();
                $selected_month = isset($_GET['month']) ? $_GET['month'] : $this->controller->getCurrentMonth();
                $this->matches = $this->controller->getMatchesByMonth($selected_year, $selected_month);
                break;
        }
    }
    
    public function displayPage() {
        $this->renderHeader();
        $this->renderForm();
        $this->renderTable();
        $this->renderFooter();
    }
    
    private function renderHeader() {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>View Reports - clean.sg</title>
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
                .date-form {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 15px;
                    margin: 20px 0;
                    flex-wrap: wrap;
                }
                .date-form select,
                .date-form input[type="date"],
                .date-form input[type="month"],
                .date-form input[type="week"] {
                    padding: 8px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                    height: 38px;
                }
                .date-form button {
                    background-color: #007bff;
                    color: white;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                    height: 38px;
                }
                .date-form button:hover {
                    background-color: #0056b3;
                }
                .date-form label {
                    margin-right: 5px;
                }
                .form-group {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }
                .week-display {
                    font-size: 0.9em;
                    color: #6c757d;
                    margin-left: 5px;
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
                .rating {
                    color: #ffc107;
                    font-weight: bold;
                }
                .review {
                    font-style: italic;
                    color: #6c757d;
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
            </style>
        </head>
        <body>
            <header>
                <h1>clean.sg</h1>
                <a href="../logout.php">Logout</a>
            </header>
            <h2>View Reports</h2>
        <?php
    }
    
    private function renderForm() {
        ?>
        <form class="date-form" method="get" action="pm_view_reports.php" id="dateForm">
            <div class="form-group">
                <select name="view_type" id="view_type" onchange="updateDateInputs()">
                    <option value="day" <?php echo $this->view_type === 'day' ? 'selected' : ''; ?>>Daily View</option>
                    <option value="week" <?php echo $this->view_type === 'week' ? 'selected' : ''; ?>>Weekly View</option>
                    <option value="month" <?php echo $this->view_type === 'month' ? 'selected' : ''; ?>>Monthly View</option>
                </select>
            </div>
            
            <div class="form-group" id="dayInput" style="display: <?php echo $this->view_type === 'day' ? 'flex' : 'none'; ?>">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($_GET['date'] ?? $this->controller->getCurrentDate()); ?>" max="<?php echo $this->controller->getCurrentDate(); ?>">
            </div>
            
            <div class="form-group" id="weekInput" style="display: <?php echo $this->view_type === 'week' ? 'flex' : 'none'; ?>">
                <label for="week">Select Week:</label>
                <input type="date" id="week" name="week" value="<?php echo htmlspecialchars($_GET['week'] ?? $this->controller->getCurrentWeekStart()); ?>" max="<?php echo $this->controller->getCurrentDate(); ?>" onchange="updateWeekDisplay(this.value)">
                <?php if ($this->view_type === 'week' && isset($this->weekRange)): ?>
                    <span class="week-display">(<?php echo htmlspecialchars($this->weekRange['display']); ?>)</span>
                <?php endif; ?>
            </div>
            
            <div class="form-group" id="monthInput" style="display: <?php echo $this->view_type === 'month' ? 'flex' : 'none'; ?>">
                <label for="year">Year:</label>
                <select name="year" id="year">
                    <?php foreach ($this->controller->getYearOptions() as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo ($_GET['year'] ?? $this->controller->getCurrentYear()) == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="month">Month:</label>
                <select name="month" id="month">
                    <?php foreach ($this->controller->getMonthOptions() as $month): ?>
                        <option value="<?php echo $month['value']; ?>" <?php echo ($_GET['month'] ?? $this->controller->getCurrentMonth()) == $month['value'] ? 'selected' : ''; ?>><?php echo $month['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">View</button>
        </form>

        <script>
            function getWeekRange(date) {
                const start = new Date(date);
                start.setDate(start.getDate() - start.getDay() + 1);
                const end = new Date(start);
                end.setDate(end.getDate() + 6);
                
                const formatDate = (d) => {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    return `${months[d.getMonth()]} ${d.getDate()}${d.getFullYear() !== start.getFullYear() ? ', ' + d.getFullYear() : ''}`;
                };
                
                return `(${formatDate(start)} - ${formatDate(end)})`;
            }

            function updateWeekDisplay(date) {
                const weekDisplay = document.querySelector('.week-display');
                if (weekDisplay) {
                    weekDisplay.textContent = getWeekRange(date);
                }
            }

            function updateDateInputs() {
                const viewType = document.getElementById('view_type').value;
                document.getElementById('dayInput').style.display = viewType === 'day' ? 'flex' : 'none';
                document.getElementById('weekInput').style.display = viewType === 'week' ? 'flex' : 'none';
                document.getElementById('monthInput').style.display = viewType === 'month' ? 'flex' : 'none';
                
                if (viewType === 'week') {
                    const weekInput = document.getElementById('week');
                    updateWeekDisplay(weekInput.value);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById('view_type').value === 'week') {
                    const weekInput = document.getElementById('week');
                    updateWeekDisplay(weekInput.value);
                }
            });
        </script>
        <?php
    }
    
    private function renderTable() {
        ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Match Date</th>
                        <th>Status</th>
                        <th>Service Title</th>
                        <th>Homeowner</th>
                        <th>Cleaner</th>
                        <th>Rating</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($this->matches)): ?>
                        <tr><td colspan="7">No matches found for the selected period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($this->matches as $match): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                                <td><?php echo htmlspecialchars($match['status']); ?></td>
                                <td><?php echo htmlspecialchars($match['service_title']); ?></td>
                                <td><?php echo htmlspecialchars($match['homeowner_first_name'] . ' ' . $match['homeowner_last_name'] . ' (' . $match['homeowner_username'] . ')'); ?></td>
                                <td><?php echo htmlspecialchars($match['cleaner_first_name'] . ' ' . $match['cleaner_last_name'] . ' (' . $match['cleaner_username'] . ')'); ?></td>
                                <td>
                                    <?php if ($match['rating']): ?>
                                        <span class="rating"><?php echo htmlspecialchars($match['rating']); ?>/5</span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($match['review']): ?>
                                        <span class="review"><?php echo htmlspecialchars($match['review']); ?></span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    private function renderFooter() {
        ?>
        <div class="back-link">
            <a href="pm_dashboard.php">← Back to Dashboard</a>
        </div>
        </body>
        </html>
        <?php
    }
}

// Main Script
$database = new Database();
$conn = $database->getConnection();
$conn->set_charset("utf8mb4");

$entity = new ReportEntity($conn);
$controller = new ReportController($entity);
$boundary = new ReportBoundary($controller);
$boundary->displayPage();
