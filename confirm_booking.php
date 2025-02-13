<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: mainlogin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $user_id = $_SESSION['user_id'];
    $bus_id = $_POST['bus_id'];
    $phone = $_POST['phone'];
    $travel_date = $_POST['date'];
    $num_passengers = $_POST['num_passengers'];
    $fare = $_POST['fare'];

    // Get bus details
    $busQuery = "SELECT * FROM buses WHERE id = ?";
    $stmt = $conn->prepare($busQuery);
    $stmt->bind_param('i', $bus_id);
    $stmt->execute();
    $busResult = $stmt->get_result();
    $bus = $busResult->fetch_assoc();

    if ($bus && $bus['seats_available'] >= $num_passengers) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert booking
            $bookingQuery = "INSERT INTO bookings (user_id, bus_no, route, phone, travel_date, num_people, price) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($bookingQuery);
            $route = $bus['from_city'] . " to " . $bus['to_city'];
            $total_price = $fare * $num_passengers;
            $stmt->bind_param('issssid', $user_id, $bus_id, $route, $phone, $travel_date, $num_passengers, $total_price);
            $stmt->execute();

            // Update available seats
            $updateSeatsQuery = "UPDATE buses SET seats_available = seats_available - ? WHERE id = ? AND seats_available >= ?";
            $stmt = $conn->prepare($updateSeatsQuery);
            $stmt->bind_param('iii', $num_passengers, $bus_id, $num_passengers);
            $stmt->execute();

            // Commit transaction
            $conn->commit();
            
            // Redirect to success page
            header("Location: profile.php?booking=success");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            header("Location: database/book.php?error=booking_failed");
            exit();
        }
    } else {
        header("Location: database/book.php?error=no_seats");
        exit();
    }
} else {
    header("Location: database/book.php");
    exit();
}
?>
