<?php
require_once 'database.php';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT poster FROM movies WHERE movie_id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($poster);
    $stmt->fetch();
    $stmt->close();

    header("Content-Type: image/jpeg");
    echo $poster;
}
