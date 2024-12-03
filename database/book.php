<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$from = $_POST['from'];
$to = $_POST['to'];
$date = $_POST['date'];

// Search for available buses
$sql = "SELECT * FROM buses WHERE from_city = ? AND to_city = ? AND departure_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $from, $to, $date);
$stmt->execute();
$result = $stmt->get_result();

// Display search results
if ($result->num_rows > 0) {
    echo "<h2>Available Buses</h2><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><strong>" . $row['bus_name'] . "</strong> | Seats: " . $row['seats_available'] . 
             " | Date: " . $row['departure_date'] . 
             "<form method='post' action='confirm_booking.php'>
                <input type='hidden' name='bus_id' value='" . $row['id'] . "' />
                <input type='hidden' name='date' value='" . $date . "' />
                <button type='submit'>Book Now</button>
              </form>
             </li>";
    }
    echo "</ul>";
} else {
    echo "<p>No buses available for the selected route and date.</p>";
}

$conn->close();
?>
