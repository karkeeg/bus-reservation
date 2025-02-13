<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header('Location: profile.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'];

// First, get the booking details to update bus seats
$sql = "SELECT b.*, bs.seats_available 
        FROM bookings b 
        LEFT JOIN buses bs ON b.bus_no = bs.bus_name 
        WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update bus seats
        $sql_update_seats = "UPDATE buses 
                           SET seats_available = seats_available + ? 
                           WHERE bus_name = ?";
        $stmt = $conn->prepare($sql_update_seats);
        $stmt->bind_param("is", $booking['num_people'], $booking['bus_no']);
        $stmt->execute();

        // Delete the booking
        $sql_delete = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "Booking cancelled successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Failed to cancel booking. Please try again.";
    }
} else {
    $_SESSION['error'] = "Booking not found or unauthorized.";
}

$stmt->close();
$conn->close();

header('Location: profile.php');
exit();
?>
