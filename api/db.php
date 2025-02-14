<?php
$host = 'localhost';
$user = 'END';
$pass = '1234';
$dbname = 'theatre_system';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>