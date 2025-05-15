<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT * FROM movies");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .card img {
            height: 300px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🎬 Welcome to MovieMania!</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <div class="movie-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($row['poster_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['title']) ?> (<?= $row['release_year'] ?>)</h5>
                    <p class="card-text"><?= htmlspecialchars($row['genre']) ?></p>
                    <a href="movie.php?id=<?= $row['movie_id'] ?>" class="btn btn-primary">View Reviews</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>