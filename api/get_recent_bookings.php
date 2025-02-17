<?php
session_start();
require_once 'db.php'; // Ensure database connection

$user_id = $_GET['user_id'];

$query = "SELECT 
            b.id AS booking_id, 
            m.id AS movie_id,
            m.title AS movie_name, 
            m.show_date, 
            m.show_time, 
            b.seats AS seat_numbers, 
            b.total, 
            b.status 
          FROM bookings b
          JOIN movies m ON b.movie_id = m.id
          WHERE b.user_id = ? 
          AND m.show_time > NOW()
          ORDER BY m.show_date ASC 
          LIMIT 5";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $decodedSeats = json_decode(json_decode($row["seat_numbers"], true), true);
    $seats = [];
    if (is_array($decodedSeats)) {
      $seats = array_merge($seats, $decodedSeats);
    }
    $bookings[] = [
      ...$row,
      "seat_numbers" => $seats,
    ];
}

echo json_encode($bookings);
?>
