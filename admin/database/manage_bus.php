<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bus_name = $_POST['bus_name'];
    $route = $_POST['route'];
    $capacity = $_POST['capacity'];

    $conn = new mysqli('localhost', 'root', '', 'bus_reservation');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO buses (bus_name, route, capacity) VALUES ('$bus_name', '$route', '$capacity')";
    if ($conn->query($sql) === TRUE) {
        echo "Bus added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>

<form action="" method="POST">
    <label for="bus_name">Bus Name:</label>
    <input type="text" id="bus_name" name="bus_name" required><br>
    <label for="route">Route:</label>
    <input type="text" id="route" name="route" required><br>
    <label for="capacity">Capacity:</label>
    <input type="number" id="capacity" name="capacity" required><br>
    <button type="submit">Add Bus</button>
</form>
