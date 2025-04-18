<?php
$host = 'localhost';
$dbname = 'nova';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function fetchAllMovies() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM movies");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchMovieById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addMovie($tmdb_id, $json, $embed_source, $release_year) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO movies (tmdb_id, json, embed_source, release_year) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$tmdb_id, $json, $embed_source, $release_year]);
}

function updateMovie($id, $tmdb_id, $json, $embed_source, $release_year) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE movies SET tmdb_id = ?, json = ?, embed_source = ?, release_year = ? WHERE id = ?");
    return $stmt->execute([$tmdb_id, $json, $embed_source, $release_year, $id]);
}

function deleteMovie($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
