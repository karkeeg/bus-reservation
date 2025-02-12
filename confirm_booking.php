<?php
include("db_connect.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $user_id = $_POST['user_id'];
    $bus_no = $_POST['bus_no'];
    $route = $_POST['route'];
    $phone = $_POST['phone'];
    $travel_date = $_POST['travel_date'];
    $num_people = $_POST['num_people'];

    // Insert booking into database
    $sql = "INSERT INTO bookings (user_id, bus_no, route,phone, travel_date, num_people)
            VALUES ('$user_id', '$bus_no', '$route', '$phone', '$travel_date', '$num_people')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Booking successful!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
