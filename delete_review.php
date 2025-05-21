<?php
session_start();
include 'db.php';

if (!isset($_POST['review_id']) || !isset($_SESSION['user_id'])) {
    die("Unauthorized.");
}

$review_id = (int)$_POST['review_id'];
$user_id = (int)$_SESSION['user_id'];

$movie_id = (int)$_POST['movie_id'];

// Checks if this review belongs to the logged-in user
$check_stmt = $conn->prepare("SELECT * FROM reviews WHERE review_id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $review_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    die("You can't delete this review.");
}

// Delete the review
$delete_stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
$delete_stmt->bind_param("i", $review_id);
$delete_stmt->execute();

header("Location: movie.php?id=" . $movie_id);
exit;
?>