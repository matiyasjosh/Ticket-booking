<?php
require_once "db.php"; // Include your database connection file

header("Content-Type: application/json");

if (!isset($_GET["user_id"])) {
    echo json_encode(["error" => "User ID is required"]);
    exit;
}

$user_id = $_GET["user_id"];

// Query to count active (unexpired) tickets
$query = "
    SELECT COUNT(*) AS active_tickets 
    FROM bookings 
    JOIN movies ON bookings.movie_id = movies.id
    WHERE bookings.user_id = ? 
    AND movies.show_time > NOW() 
    AND bookings.status = 'confirmed'
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["active_tickets" => $row["active_tickets"]]);

$stmt->close();
$conn->close();
?>
