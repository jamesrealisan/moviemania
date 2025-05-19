<?php
header('Content-Type: application/json');

$cacheFile = __DIR__ . '/cache/recommended_movies.json';
$cacheTime = 3600; // cache lifetime in seconds (1 hour)

$apiKey = '7ab7b8e64f4b031bbd2f0744a187b129';
$apiUrl = "https://api.themoviedb.org/3/trending/movie/week?api_key=$apiKey";

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    // Serve cached content
    echo file_get_contents($cacheFile);
    exit;
}

// Fetch fresh data from TMDb
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    // Save to cache file
    if (!is_dir(__DIR__ . '/cache')) {
        mkdir(__DIR__ . '/cache', 0755, true);
    }
    file_put_contents($cacheFile, $response);
    echo $response;
} else {
    // Return error JSON
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch recommended movies']);
}
?>