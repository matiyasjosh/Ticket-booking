<?php
require 'db.php';

if (!isset($_GET["movie_id"])) {
    echo json_encode(["error" => "movie_id not set"]);
    exit;
}

$movie_id = $_GET["movie_id"];
$seats = [];

$stmt = $conn->prepare("SELECT seats FROM bookings WHERE movie_id = ? AND status != 'cancelled'");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Decode twice to fix double-encoded issue
    $decodedSeats = json_decode(json_decode($row["seats"], true), true);

    if ($decodedSeats === null) {
        error_log("JSON decode failed for seats: " . $row["seats"]);
        continue;
    }

    if (is_array($decodedSeats)) {
        $seats = array_merge($seats, $decodedSeats);
    }
}

$seats = array_values(array_unique($seats));

echo json_encode(["booked_seats" => $seats]);
?>
