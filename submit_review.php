<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = trim($_POST['comment'] ?? '');

// Validate presence
if (!$movie_id || !$rating || !$comment) {
    die("All fields are required.");
}

// Validate rating is an integer between 1 and 5
if (!filter_var($rating, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]])) {
    die("Rating must be between 1 and 5 stars.");
}

// Optionally validate comment length
if (strlen($comment) < 3 || strlen($comment) > 1000) {
    die("Comment must be between 3 and 1000 characters.");
}

$stmt = $conn->prepare("INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $movie_id, $rating, $comment);
$stmt->execute();
$stmt->close();

header("Location: movie.php?id=" . urlencode($movie_id));
exit();
?>
