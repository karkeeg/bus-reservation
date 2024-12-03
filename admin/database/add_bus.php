<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$bus_name = $_POST['bus_name'];
$from_city = $_POST['from_city'];
$to_city = $_POST['to_city'];
$departure_date = $_POST['departure_date'];
$seats_available = $_POST['seats_available'];

// Insert data into the database
$sql = "INSERT INTO buses (bus_name, from_city, to_city, departure_date, seats_available) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssi', $bus_name, $from_city, $to_city, $departure_date, $seats_available);

if ($stmt->execute()) {
    echo "<p>New bus schedule added successfully!</p>";
} else {
    echo "<p>Error: " . $stmt->error . "</p>";
}

$conn->close();
?>
