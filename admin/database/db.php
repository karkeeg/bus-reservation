<?php
$servername = "localhost";
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "bus_reservation";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}

?>
