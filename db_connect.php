<?php
$servername = "localhost";
$username = "root"; // Default for XAMPP, change if needed
$password = ""; // Default for XAMPP, change if needed
$dbname = "bus_reservation"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
