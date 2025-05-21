<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    die("Movie ID missing.");
}

$movie_id = (int) $_GET['id'];
$api_key = '7ab7b8e64f4b031bbd2f0744a187b129';

// Fetch movie details from TMDb
$movie_url = "https://api.themoviedb.org/3/movie/{$movie_id}?api_key={$api_key}";
$movie_data = json_decode(file_get_contents($movie_url), true);

if (!$movie_data) {
    die("Movie not found.");
}

// Get average rating
$avg_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE movie_id = ?");
$avg_stmt->bind_param("i", $movie_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result()->fetch_assoc();
$avg_rating = round($avg_result['avg_rating'], 1);

// Get all reviews
$review_stmt = $conn->prepare("SELECT r.review_id, r.rating, r.comment, r.created_at, r.user_id, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.movie_id = ? 
    ORDER BY r.created_at DESC");
$review_stmt->bind_param("i", $movie_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($movie_data['title']) ?> - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/movie.css" rel="stylesheet" />
</head>
<body>
<div class="container my-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <div class="row">
        <div class="col-md-4">
            <img src="https://image.tmdb.org/t/p/w500<?= $movie_data['poster_path'] ?>" class="img-fluid" alt="<?= htmlspecialchars($movie_data['title']) ?>">
        </div>
        <div class="col-md-8">
            <h2><?= htmlspecialchars($movie_data['title']) ?> (<?= substr($movie_data['release_date'], 0, 4) ?>)</h2>
            <p><strong>Genre:</strong> <?= implode(", ", array_map(fn($g) => $g['name'], $movie_data['genres'])) ?></p>
            <p><strong>Overview:</strong> <?= htmlspecialchars($movie_data['overview']) ?></p>

            <?php if ($avg_rating): ?>
                <p><strong>Average Rating:</strong>
                    <span class="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi <?= $i <= round($avg_rating) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                        <?php endfor; ?>
                    </span> <?= $avg_rating ?>/5
                </p>
            <?php else: ?>
                <p>No ratings yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <?php if (isset($_SESSION['user_id'])): ?>
    <h4>Submit Your Review</h4>
    <form method="POST" action="submit_review.php">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <input type="hidden" name="title" value="<?= htmlspecialchars($movie_data['title']) ?>">
        <input type="hidden" name="release_year" value="<?= substr($movie_data['release_date'], 0, 4) ?>">
        <input type="hidden" name="poster_url" value="https://image.tmdb.org/t/p/w500<?= $movie_data['poster_path'] ?>">
        <input type="hidden" name="genre" value="<?= implode(", ", array_map(fn($g) => $g['name'], $movie_data['genres'])) ?>">

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select class="form-select" name="rating" required>
                <option value="">Choose...</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea class="form-control" name="comment" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Review</button>
    </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to post a review.</p>
    <?php endif; ?>

    <hr>

    <h4>User Reviews</h4>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="mb-3 border p-3 rounded">
                <strong><?= htmlspecialchars($review['username']) ?></strong>
                <span class="text-warning">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi <?= $i <= $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                    <?php endfor; ?>
                </span>
                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                <small class="text-muted"><?= $review['created_at'] ?></small>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
    <form method="POST" action="delete_review.php" onsubmit="return confirm('Are you sure you want to delete this review?');">
        <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
        <button type="submit" class="btn btn-sm btn-danger mt-2">Delete</button>
    </form>
<?php endif; ?>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap Icons + jQuery -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>