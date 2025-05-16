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
        body {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
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
    <form id="searchForm" class="d-flex mb-4" role="search">
    <input class="form-control me-2" type="search" id="searchInput" placeholder="Search movies..." aria-label="Search">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>

<div id="searchResults" class="movie-grid"></div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🎬 Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'to MovieMania') ?>!</h2>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const API_KEY = '7ab7b8e64f4b031bbd2f0744a187b129';

$('#searchForm').submit(function(e) {
    e.preventDefault();
    const query = $('#searchInput').val();

    $.get(`https://api.themoviedb.org/3/search/movie?api_key=${API_KEY}&query=${query}`, function(data) {
        let results = "";
        data.results.forEach(movie => {
            results += `
                <div class="card">
                    <img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" class="card-img-top" />
                    <div class="card-body">
                        <h5 class="card-title">${movie.title} (${(movie.release_date || '').split('-')[0]})</h5>
                        <a href="movie.php?id=${movie.id}" class="btn btn-primary">Rate & Review</a>
                    </div>
                </div>
            `;
        });
        $('#searchResults').html(results);
    });
});
</script>

</body>
</html>
