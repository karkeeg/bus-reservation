<?php
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Check available seats
        $seatCheckSql = "SELECT seats_available FROM buses WHERE id = ? FOR UPDATE";
        $seatStmt = $conn->prepare($seatCheckSql);
        $seatStmt->bind_param('i', $bus_id);
        $seatStmt->execute();
        $seatResult = $seatStmt->get_result();

        if ($seatResult->num_rows > 0) {
            $bus = $seatResult->fetch_assoc();
            $seatsAvailable = $bus['seats_available'];

            if ($seatsAvailable > 0) {
                // Deduct 1 seat
                $updateSeatsSql = "UPDATE buses SET seats_available = seats_available - 1 WHERE id = ?";
                $updateStmt = $conn->prepare($updateSeatsSql);
                $updateStmt->bind_param('i', $bus_id);
                $updateStmt->execute();

                // Record the booking
                $insertBookingSql = "INSERT INTO bookings (bus_id, name, phone, booking_date) VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertBookingSql);
                $insertStmt->bind_param('isss', $bus_id, $name, $phone, $date);
                $insertStmt->execute();

                // Commit transaction
                $conn->commit();
                echo "Booking successful! Thank You <h1>$name</h1>";
            } else {
                echo "No seats available!";
            }
        } else {
            echo "Bus not found!";
        }
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on error
        echo "Booking failed: " . $e->getMessage();
    }
}
?>