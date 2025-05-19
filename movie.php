<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$movie_id = $_GET['id'] ?? null;
if (!$movie_id) {
    die("Movie ID is required.");
}

$stmt = $conn->prepare("
    SELECT r.*, u.username
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.movie_id = ?
");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

// If not found, fetch from TMDB
if (!$movie) {
    $tmdb_api_key = '7ab7b8e64f4b031bbd2f0744a187b129';
    $tmdb_url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$tmdb_api_key";
    $tmdb_data = json_decode(file_get_contents($tmdb_url), true);

    if (isset($tmdb_data['id'])) {
        $movie = [
            'movie_id' => $tmdb_data['id'],
            'title' => $tmdb_data['title'],
            'release_year' => substr($tmdb_data['release_date'] ?? '', 0, 4),
            'poster_url' => "https://image.tmdb.org/t/p/w500" . $tmdb_data['poster_path'],
            'genre' => implode(', ', array_column($tmdb_data['genres'], 'name')),
        ];
    } else {
        die("Movie not found.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star-rating {
            direction: rtl;
            display: inline-flex;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2rem;
            color: lightgray;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: gold;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <div class="row mb-4">
        <div class="col-md-4">
            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($movie['title']) ?>">
        </div>
        <div class="col-md-8">
            <h2><?= htmlspecialchars($movie['title']) ?> (<?= $movie['release_year'] ?>)</h2>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
        </div>
    </div>

    <h4>Leave a Review</h4>
    <form action="submit_review.php" method="POST">
        <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie['movie_id']) ?>">
        <div class="mb-3">
            <label class="form-label">Your Rating:</label><br>
            <div class="star-rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Your Review:</label>
            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Submit Review</button>
    </form>

    <hr>
    <h4>Reviews</h4>
    <?php
    $review_query = $conn->prepare("
        SELECT r.rating, r.comment, r.created_at, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.movie_id = ?
        ORDER BY r.created_at DESC
    ");
    $review_query->bind_param("i", $movie['movie_id']);
    $review_query->execute();
    $reviews = $review_query->get_result();

    if ($reviews->num_rows > 0):
        while ($review = $reviews->fetch_assoc()):
    ?>
    <div class="card mb-2">
        <div class="card-body">
            <h6>
                <?= htmlspecialchars($review['username']) ?> 
                rated: <?= str_repeat("★", (int)$review['rating']) ?>
            </h6>
            <p><?= htmlspecialchars($review['comment']) ?></p>
            <small class="text-muted"><?= $review['created_at'] ?></small>
        </div>
    </div>
    <?php endwhile; else: ?>
        <p>No reviews yet. Be the first to review this movie!</p>
    <?php endif; ?>
</div>
</body>
</html>