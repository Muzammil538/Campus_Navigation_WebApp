<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comments = sanitizeInput($_POST['comments']);
    $category = sanitizeInput($_POST['category']);
    
    if ($rating < 1 || $rating > 5) {
        $error = 'Please provide a rating';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO feedback (user_id, category, rating, comments) 
                  VALUES (:user_id, :category, :rating, :comments)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comments', $comments);
        
        if ($stmt->execute()) {
            setFlashMessage('success', 'Thank you for your feedback!');
            redirect('map.php');
        } else {
            $error = 'Failed to submit feedback';
        }
    }
}

$pageTitle = 'Share Your Feedback';
$customCSS = 'feedback.css';
?>
<?php include 'includes/header.php'; ?>

<div class="page-container">
    <header class="top-bar">
        <button class="back-btn" onclick="window.history.back()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <h1>Share Your Feedback</h1>
        <a href="map.php" class="skip-btn">Skip</a>
    </header>

    <div class="content">
        <?php if ($error): ?>
            <div class="error-message show"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="rating-section">
            <h2>Rate your experience</h2>
            <div class="star-rating" id="starRating">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <button type="button" class="star" data-rating="<?php echo $i; ?>" onclick="setRating(<?php echo $i; ?>)">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <form class="feedback-form" method="POST" action="feedback.php">
            <input type="hidden" name="rating" id="ratingInput" value="0">
            
            <div class="form-group">
                <label>Comments</label>
                <textarea 
                    name="comments" 
                    placeholder="Tell us what you think..."
                    rows="6"
                    required
                ><?php echo isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : ''; ?></textarea>
            </div>

            <div class="category-group">
                <label>Category</label>
                <div class="category-options">
                    <button type="button" class="category-btn" data-category="bug" onclick="setCategory('bug')">Bug Report</button>
                    <button type="button" class="category-btn" data-category="feature" onclick="setCategory('feature')">Feature Request</button>
                    <button type="button" class="category-btn active" data-category="general" onclick="setCategory('general')">General Feedback</button>
                </div>
                <input type="hidden" name="category" id="categoryInput" value="general">
            </div>

            <button type="submit" class="btn-submit">Submit Feedback</button>
        </form>
    </div>

    <?php include 'includes/bottom-nav.php'; ?>
</div>

<script>
let selectedRating = 0;
let selectedCategory = 'general';

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('ratingInput').value = rating;
    
    // Update stars
    document.querySelectorAll('.star').forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
            star.querySelector('path').setAttribute('fill', '#F39C12');
        } else {
            star.classList.remove('active');
            star.querySelector('path').setAttribute('fill', 'none');
        }
    });
    
    <?php if ($_SESSION['voice_navigation']): ?>
    speak(`${rating} star${rating > 1 ? 's' : ''} selected`);
    <?php endif; ?>
}

function setCategory(category) {
    selectedCategory = category;
    document.getElementById('categoryInput').value = category;
    
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.querySelector(`[data-category="${category}"]`).classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
