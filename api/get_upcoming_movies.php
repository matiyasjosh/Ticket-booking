<?php
require_once "db.php"; // Include your database connection file

header("Content-Type: application/json");

// Query to count active (unexpired) tickets
$query = "
    SELECT COUNT(*) AS upcoming_movies 
    FROM movies 
    WHERE show_time > NOW() 
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Debugging information
error_log("upcoming 1birr's: " . json_encode($row));

echo json_encode(["upcoming_movies" => $row["upcoming_movies"]]);

$stmt->close();
$conn->close();
?>