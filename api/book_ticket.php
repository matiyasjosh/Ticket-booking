<?php
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST["user_id"]; // Get from session or input
    $movie_id = $_POST["movie_id"];
    $seats = json_encode($_POST["seats"]); // Convert seat array to JSON
    $total = $_POST["total"];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, movie_id, seats, total) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisd", $user_id, $movie_id, $seats, $total);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Booking successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }

    $stmt->close();
}
?>
