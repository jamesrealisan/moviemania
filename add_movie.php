<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $release_year = $_POST['release_year'];
    $poster_url = $_POST['poster_url'];
    $genre = $_POST['genre'];

    $stmt = $conn->prepare("INSERT INTO movies (title, release_year, poster_url, genre) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $title, $release_year, $poster_url, $genre);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>