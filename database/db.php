<?php
$servername = "localhost";
$username = "bus_reservation"; 
$password = ""; 
$dbname = "bus_reservation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
