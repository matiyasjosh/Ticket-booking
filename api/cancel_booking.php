<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$booking_id = $data['booking_id'];


$query = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to cancel booking"]);
}
?>
