<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'], $_POST['trailer_url'], $_FILES['poster'])) {
        $title = trim($_POST['title']);
        $trailerUrl = trim($_POST['trailer_url']);
        $poster = file_get_contents($_FILES['poster']['tmp_name']);
        $null = NULL;

        if (preg_match('/youtu\.be\/([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        }

        $stmt = $conn->prepare("INSERT INTO upcoming_movies (title, trailer_url, poster) VALUES (?, ?, ?)");
        $stmt->bind_param("ssb", $title, $trailerUrl, $null);
        $stmt->send_long_data(2, $poster);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    } else {
        echo "Missing required fields.";
    }
}
