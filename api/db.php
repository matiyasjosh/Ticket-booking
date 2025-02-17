<?php
$host = 'localhost';
$user = 'matijosh';
$pass = '12@mJ/2024';
$dbname = 'theatre_system';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>