<?php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$userEmail = $_SESSION['email'];
$movieId = $data['movie_id'];
$theater = $data['theater'];
$seat = $data['seat'];
$paymentMethod = $data['payment_method'];

$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$userId = $user['user_id'];

$insert = $conn->prepare("INSERT INTO bookings (user_id, movie_id, theater_name, seat_number, payment_method) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("iisss", $userId, $movieId, $theater, $seat, $paymentMethod);

if ($insert->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
