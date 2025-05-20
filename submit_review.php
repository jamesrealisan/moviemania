<?php
ob_start(); // Start output buffering
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? null;

$movie_id = $_POST['movie_id'];
$title = $_POST['title'];
$year = $_POST['release_year'];
$genre = $_POST['genre'];
$poster_url = $_POST['poster_url'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

// Check if the movie exists
$check = $conn->prepare("SELECT movie_id FROM movies WHERE movie_id = ?");
$check->bind_param("i", $movie_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $insertMovie = $conn->prepare("INSERT INTO movies (movie_id, title, release_year, genre, poster_url) VALUES (?, ?, ?, ?, ?)");
    $insertMovie->bind_param("isiss", $movie_id, $title, $year, $genre, $poster_url);
    $insertMovie->execute();
}

// Insert the review
$reviewStmt = $conn->prepare("INSERT INTO reviews (user_id, movie_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
$reviewStmt->bind_param("iiis", $user_id, $movie_id, $rating, $comment);
$reviewStmt->execute();

// Debug header issue
if (headers_sent($file, $line)) {
    echo "Headers already sent in $file on line $line";
    exit;
}

$movie_id_clean = urlencode(trim($movie_id));  // Remove newlines, spaces, etc.
header("Location: movie.php?id=$movie_id_clean");
exit();