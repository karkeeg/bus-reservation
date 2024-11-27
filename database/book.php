<!-- INSERT INTO `buses` (`id`, `from`, `to`, `date`, `bus_name`, `departure_time`, `Fare`) 
 VALUES ('1', 'Kathmandu', 'Pokhara', '2024-12-01', 'Surjay Travels', '07:00:00', '1500.00');
 
 INSERT INTO `buses` (`id`, `from`, `to`, `date`, `bus_name`, `departure_time`, `Fare`) 
 VALUES ('2', 'Kathmandu', 'Chitwan', '2024-12-01', 'Karkee Travels', '08:00:00', '1800.00');
 -->

 <?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Change if your MySQL has a different root password
$dbname = "bus_search";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get search parameters
$from = isset($_GET['from']) ? $conn->real_escape_string($_GET['from']) : '';
$to = isset($_GET['to']) ? $conn->real_escape_string($_GET['to']) : '';
$date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

if (!$from || !$to || !$date) {
    echo json_encode(["error" => "Please provide 'from', 'to', and 'date' parameters."]);
    exit;
}

// Query the database
$sql = "SELECT * FROM buses WHERE `from` = '$from' AND `to` = '$to' AND `date` = '$date'";
$result = $conn->query($sql);

// Process results
if ($result->num_rows > 0) {
    $buses = [];
    while ($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
    echo json_encode($buses);
} else {
    echo json_encode(["message" => "No buses found for this route."]);
}

// Close connection
$conn->close();
?>
