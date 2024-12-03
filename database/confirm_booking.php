<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get booking details
$bus_id = $_POST['bus_id'];
$date = $_POST['date'];

// Decrease seat count
$sql = "UPDATE buses SET seats_available = seats_available - 1 WHERE id = ? AND seats_available > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $bus_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Insert into bookings table
    $sql = "INSERT INTO bookings (bus_id, travel_date) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $bus_id, $date);
    $stmt->execute();
    
    echo "<p>Booking confirmed!</p>";
} else {
    echo "<p>Booking failed. No seats available.</p>";
}

$conn->close();
?>
