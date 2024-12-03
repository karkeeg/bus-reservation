<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$bus_id = $_POST['bus_id'];
$date = $_POST['date'];
$name = $_POST['name'];
$phone = $_POST['phone'];

// Insert booking data
$sql = "INSERT INTO bookings (bus_id,name, phone, booking_date) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isss', $bus_id, $name, $phone, $date);

if ($stmt->execute()) {
    echo "<p>Booking confirmed! Thank you, " . htmlspecialchars($name) . ".</p>";
} else {
    echo "<p>Error: " . $conn->error . "</p>";
}

$conn->close();
?>
