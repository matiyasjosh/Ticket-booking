<?php
$host = 'localhost';
$user = 'Your user name';
$pass = 'Your Password';
$dbname = 'theatre_system';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
