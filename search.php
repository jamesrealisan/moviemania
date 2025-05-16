<?php
$apiKey = "7ab7b8e64f4b031bbd2f0744a187b129";
$query = isset($_GET['query']) ? urlencode($_GET['query']) : '';

$apiUrl = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query";

$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container mt-4">
    <h2 class="mb-4">Search TMDb Movies</h2>
    <form method="GET" class="mb-4">
        <input type="text" name="query" class="form-control" placeholder="Search for a movie..." required value="<?= htmlspecialchars($query) ?>">
        <button class="btn btn-primary mt-2" type="submit">Search</button>
    </form>

    <div class="row">
        <?php if (!empty($data['results'])): ?>
            <?php foreach ($data['results'] as $movie): ?>
                <div class="col-md-3 mb-4">
                    <div class="card text-dark">
                        <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" class="card-img-top" alt="<?= $movie['title'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $movie['title'] ?></h5>
                            <p class="card-text">Year: <?= substr($movie['release_date'], 0, 4) ?></p>
                            <form method="POST" action="add_movie.php">
                                <input type="hidden" name="title" value="<?= $movie['title'] ?>">
                                <input type="hidden" name="release_year" value="<?= substr($movie['release_date'], 0, 4) ?>">
                                <input type="hidden" name="poster_url" value="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>">
                                <input type="hidden" name="genre" value="Unknown">
                                <button type="submit" class="btn btn-success btn-sm">Add to MovieMania</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif ($query): ?>
            <p>No movies found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
