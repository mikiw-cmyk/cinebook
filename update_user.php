<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $email, $role, $userId);
    $stmt->execute();

    header("Location: admin_page.php");
    exit();
}
