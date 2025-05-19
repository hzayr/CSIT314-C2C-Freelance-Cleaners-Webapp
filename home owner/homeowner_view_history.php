<?php
session_start();
require_once "../connectDatabase.php";

class MatchEntity
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAcceptedMatchesByHomeowner($homeowner_id)
    {
        $stmt = $this->conn->prepare("
            SELECT m.match_id, m.service_id, m.match_date, m.status,
                   cs.service_title, sc.category as service_category, cs.service_price,
                   p.first_name, p.last_name, m.rating, m.review
            FROM matches m
            JOIN cleaningservices cs ON m.service_id = cs.service_id
            JOIN users u ON cs.cleaner_id = u.user_id
            JOIN profile p ON cs.cleaner_id = p.user_id
            JOIN service_categories sc ON cs.service_category = sc.category_id
            WHERE m.homeowner_id = ? AND m.status = 'accepted'
            ORDER BY m.match_date DESC
        ");
        $stmt->bind_param("i", $homeowner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }

    public function addReview($match_id, $rating, $review)
    {
        $stmt = $this->conn->prepare("
            UPDATE matches 
            SET rating = ?, review = ?
            WHERE match_id = ?
        ");
        $stmt->bind_param("isi", $rating, $review, $match_id);
        return $stmt->execute();
    }

    public function searchMatches($homeowner_id, $criteria, $search)
    {
        $stmt = $this->conn->prepare("
            SELECT m.match_id, m.service_id, m.match_date, m.status,
                   cs.service_title, sc.category as service_category, cs.service_price,
                   p.first_name, p.last_name, u.email, u.phone_num,
                   m.rating, m.review
            FROM matches m
            JOIN cleaningservices cs ON m.service_id = cs.service_id
            JOIN users u ON cs.cleaner_id = u.user_id
            JOIN profile p ON cs.cleaner_id = p.user_id
            JOIN service_categories sc ON cs.service_category = sc.category_id
            WHERE m.homeowner_id = ? AND m.status = 'accepted'
            AND (
                cs.service_title LIKE ? OR
                sc.category LIKE ? OR
                cs.service_price LIKE ? OR
                CONCAT(p.first_name, ' ', p.last_name) LIKE ? OR
                u.email LIKE ? OR
                u.phone_num LIKE ?
            )
            ORDER BY m.match_date DESC
        ");
        $stmt->bind_param("issssss", $homeowner_id, $search, $search, $search, $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        return $matches;
    }
}

class ViewHistoryController
{
    private $matchEntity;

    public function __construct($matchEntity)
    {
        $this->matchEntity = $matchEntity;
    }

    public function getHomeownerID()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getMatches()
    {
        $homeownerID = $this->getHomeownerID();
        return $homeownerID !== null ? $this->matchEntity->getAcceptedMatchesByHomeowner($homeownerID) : [];
    }
}

class ViewHistoryPage
{
    private $controller;
    private $matches;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function setMatches($matches)
    {
        $this->matches = $matches;
    }

    public function ViewHistoryUI()
    {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>My Service History</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f8f9fa;
                }
                header {
                    padding: 20px;
                    background-color: #343a40;
                    color: #ffffff;
                }
                header h1 {
                    margin: 0;
                    font-size: 1.5em;
                }
                h2 {
                    text-align: center;
                    color: #343a40;
                    margin-top: 20px;
                }
                table {
                    width: 90%;
                    margin: 20px auto;
                    border-collapse: collapse;
                    background-color: #ffffff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                table, th, td {
                    border: 1px solid #dee2e6;
                }
                th, td {
                    padding: 12px;
                    text-align: center;
                    color: #343a40;
                    max-width: 200px;
                    word-wrap: break-word;
                }
                th {
                    background-color: #6c757d;
                    color: #ffffff;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f1f1f1;
                }
                .return-button {
                    display: inline-block;
                    margin: 20px;
                    padding: 10px 15px;
                    background-color: #6c757d;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .return-button:hover {
                    background-color: #5a6268;
                }
                .matches-button {
                    display: inline-block;
                    margin: 20px;
                    padding: 10px 15px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .matches-button:hover {
                    background-color: #0056b3;
                }
                .status-accepted {
                    color: #28a745;
                    font-weight: bold;
                }
                .review-button {
                    padding: 8px 16px;
                    background-color: #28a745;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .review-button:hover {
                    background-color: #218838;
                }
                .review-button:disabled {
                    background-color: #6c757d;
                    cursor: not-allowed;
                }
                .popup-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                }
                .popup-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: white;
                    padding: 20px;
                    border-radius: 5px;
                    width: 400px;
                }
                .rating-container {
                    margin: 20px 0;
                    text-align: center;
                }
                .star {
                    font-size: 24px;
                    color: #ffd700;
                    cursor: pointer;
                    margin: 0 5px;
                }
                .review-textarea {
                    width: 100%;
                    height: 100px;
                    margin: 10px 0;
                    padding: 8px;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    resize: vertical;
                }
                .popup-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                    margin-top: 20px;
                }
                .popup-submit, .popup-cancel {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .popup-submit {
                    background-color: #28a745;
                    color: white;
                }
                .popup-cancel {
                    background-color: #6c757d;
                    color: white;
                }
                .popup-submit:hover {
                    background-color: #218838;
                }
                .popup-cancel:hover {
                    background-color: #5a6268;
                }
                .search-form {
                    text-align: center;
                    margin: 20px 0;
                }
                .search-form select,
                .search-form input[type="text"] {
                    padding: 8px;
                    margin: 0 5px;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                }
                .search-button {
                    background-color: #007bff;
                    color: white;
                    text-align: center;
                    text-decoration: none;
                    border-radius: 5px;
                    padding: 8px 16px;
                    border: none;
                    cursor: pointer;
                }
                .search-button:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <!-- Review popup -->
            <div id="reviewPopup" class="popup-overlay">
                <div class="popup-content">
                    <h3>Add Review</h3>
                    <div class="rating-container">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <textarea id="reviewText" class="review-textarea" placeholder="Write your review here..."></textarea>
                    <div class="popup-buttons">
                        <button id="submitReview" class="popup-submit">Submit</button>
                        <button id="cancelReview" class="popup-cancel">Cancel</button>
                    </div>
                </div>
            </div>

            <header>
                <h1>clean.sg</h1>
            </header>
            <h2>My Service History</h2>

            <form method="POST" action="homeowner_view_history.php" class="search-form">
                <label for="service">Search based on:</label>
                <select id="service" name="criteria">
                    <option value="service_title">Service Title</option>
                    <option value="category">Category</option>
                    <option value="service_price">Price</option>
                    <option value="first_name">Cleaner First Name</option>
                    <option value="last_name">Cleaner Last Name</option>
                </select>
                <input type="text" id="search" name="search" placeholder="Enter Text Here" />
                <button class="search-button" type="submit" name="searchButton">Search</button>
            </form>

            <table>
                <tr>
                    <th>Service Title</th>
                    <th>Service Category</th>
                    <th>Price</th>
                    <th>Cleaner</th>
                    <th>Match Date</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($this->matches as $match): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($match['service_title']); ?></td>
                        <td><?php echo htmlspecialchars($match['service_category']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($match['service_price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($match['first_name'] . " " . $match['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['match_date']); ?></td>
                        <td class="status-accepted">Accepted</td>
                        <td>
                            <?php if ($match['rating']): ?>
                                <?php echo str_repeat('★', $match['rating']); ?>
                            <?php else: ?>
                                Not rated
                            <?php endif; ?>
                        </td>
                        <td><?php echo $match['review'] ? htmlspecialchars($match['review']) : 'No review yet'; ?></td>
                        <td>
                            <button 
                                class="review-button" 
                                onclick="showReviewPopup(<?php echo $match['match_id']; ?>)"
                                <?php echo ($match['rating'] && $match['review']) ? 'disabled' : ''; ?>
                            >
                                <?php echo ($match['rating'] && $match['review']) ? 'Reviewed' : 'Add Review'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div style="text-align: center; margin: 20px;">
                <a href="homeowner_dashboard.php" class="return-button">Return to Main Page</a>
                <a href="homeowner_view_matches.php" class="matches-button">Return to Matches</a>
            </div>

            <script>
                let currentMatchId = null;
                let selectedRating = 0;

                function showReviewPopup(matchId) {
                    currentMatchId = matchId;
                    selectedRating = 0;
                    document.getElementById('reviewPopup').style.display = 'block';
                    document.getElementById('reviewText').value = '';
                    updateStars(0);
                }

                function updateStars(rating) {
                    const stars = document.querySelectorAll('.star');
                    stars.forEach((star, index) => {
                        star.style.color = index < rating ? '#ffd700' : '#ccc';
                    });
                }

                document.querySelectorAll('.star').forEach(star => {
                    star.addEventListener('click', function() {
                        selectedRating = parseInt(this.dataset.rating);
                        updateStars(selectedRating);
                    });

                    star.addEventListener('mouseover', function() {
                        updateStars(parseInt(this.dataset.rating));
                    });

                    star.addEventListener('mouseout', function() {
                        updateStars(selectedRating);
                    });
                });

                document.getElementById('submitReview').onclick = function() {
                    if (!selectedRating) {
                        alert('Please select a rating');
                        return;
                    }

                    const review = document.getElementById('reviewText').value.trim();
                    if (!review) {
                        alert('Please write a review');
                        return;
                    }

                    // Submit the review via AJAX
                    fetch('add_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `match_id=${currentMatchId}&rating=${selectedRating}&review=${encodeURIComponent(review)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error saving review. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while saving the review. Please try again.');
                    });
                };

                document.getElementById('cancelReview').onclick = function() {
                    document.getElementById('reviewPopup').style.display = 'none';
                    currentMatchId = null;
                    selectedRating = 0;
                };

                // Close popup when clicking outside
                document.getElementById('reviewPopup').onclick = function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                        currentMatchId = null;
                        selectedRating = 0;
                    }
                };
            </script>
        </body>
        </html>
        <?php
    }
}

// Main script
$database = new Database();
$conn = $database->getConnection();

$matchEntity = new MatchEntity($conn);
$controller = new ViewHistoryController($matchEntity);
$page = new ViewHistoryPage($controller);

// Handle search functionality
if (isset($_POST['searchButton'])) {
    $criteria = $_POST['criteria'] ?? '';
    $search = '%' . $_POST['search'] . '%';  // Add wildcards for partial matching
    $matches = $matchEntity->searchMatches($controller->getHomeownerID(), $criteria, $search);
} else {
    $matches = $controller->getMatches();
}

$page->setMatches($matches);
$page->ViewHistoryUI();

$database->closeConnection();
?> 