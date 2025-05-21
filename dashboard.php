<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT m.* FROM movies m 
                        JOIN reviews r ON m.movie_id = r.movie_id 
                        WHERE r.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet" />
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

    <h3 style="color: white; font-weight: bold;">Recommended Movies</h3>
    <div id="recommendedMovies" class="movie-grid">
        <!-- Recommended movies from TMDb will load here -->
        <p>Loading recommended movies...</p>
    </div>

    <h3 style="color: white; font-weight: bold;">Your Movies</h3>
<div id="localMovies" class="movie-grid">
    <?php if ($result->num_rows === 0): ?>
        <p style="color: white; font-weight: bold;">You haven't reviewed any movies yet.</p>
    <?php endif; ?>

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

// Fetch trending movies on page load
function loadRecommendedMovies() {
    $.get('recommended.php', function(data) {
        let results = "";
        if (!data.results || data.results.length === 0) {
            results = "<p>No recommended movies available at the moment.</p>";
        } else {
            data.results.forEach(movie => {
                const poster = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : 'https://via.placeholder.com/300x450?text=No+Image';
                const releaseYear = movie.release_date ? movie.release_date.split('-')[0] : 'N/A';
                results += `
                    <div class="card">
                        <img src="${poster}" class="card-img-top" alt="${movie.title}">
                        <div class="card-body">
                            <h5 class="card-title">${movie.title} (${releaseYear})</h5>
                            <a href="movie.php?id=${movie.id}" class="btn btn-primary">Rate & Review</a>
                        </div>
                    </div>
                `;
            });
        }
        $('#recommendedMovies').html(results);
    }).fail(() => {
        $('#recommendedMovies').html('<p>Failed to load recommended movies. Please try again later.</p>');
    });
}

$('#searchForm').submit(function(e) {
    e.preventDefault();
    const query = $('#searchInput').val().trim();
    if (query.length === 0) return;

    $.get(`https://api.themoviedb.org/3/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}`, function(data) {
        let results = "";
        if (data.results.length === 0) {
            results = "<p>No movies found for your search.</p>";
        } else {
            data.results.forEach(movie => {
                const poster = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : 'https://via.placeholder.com/300x450?text=No+Image';
                const releaseYear = movie.release_date ? movie.release_date.split('-')[0] : 'N/A';
                results += `
                    <div class="card">
                        <img src="${poster}" class="card-img-top" alt="${movie.title}">
                        <div class="card-body">
                            <h5 class="card-title">${movie.title} (${releaseYear})</h5>
                            <a href="movie.php?id=${movie.id}" class="btn btn-primary">Rate & Review</a>
                        </div>
                    </div>
                `;
            });
        }
        $('#searchResults').html(results);
    }).fail(() => {
        $('#searchResults').html('<p>Search failed. Please try again later.</p>');
    });
});

// Load recommended movies on page ready
$(document).ready(function() {
    loadRecommendedMovies();
});
</script>

</body>
</html>