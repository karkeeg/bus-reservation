<?php
session_start();
include("db_connect.php"); // Make sure to include the database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id']; 

// Fetch user details from the database
$sql_user = "SELECT * FROM users WHERE id = '$user_id'";
$result_user = mysqli_query($conn, $sql_user);

// Check if user exists
if (mysqli_num_rows($result_user) > 0) {
    $user = mysqli_fetch_assoc($result_user);
} else {
    // If user details not found, redirect to login
    header('Location: login.php');
    exit();
}

// Fetch the user's bookings from the database
$sql_bookings = "SELECT * FROM bookings WHERE bus_id = '$user_id'";
$result_bookings = mysqli_query($conn, $sql_bookings);

// Debug: Check if the query for bookings returns results
// echo "Bookings query: " . $sql_bookings; 
// echo "Number of bookings: " . mysqli_num_rows($result_bookings);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- You can link to an external CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header h2 {
            margin: 0;
            font-size: 2rem;
        }
        .profile-details {
            margin-bottom: 20px;
        }
        .profile-details p {
            margin: 10px 0;
            font-size: 1rem;
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .booking-table th, .booking-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .booking-table th {
            background-color: #f2f2f2;
        }
        .booking-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="profile-header">
            <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
            <p>Here's your profile and booking information.</p>
        </div>

        <div class="profile-details">
            <h3>Your Details:</h3>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <div class="bookings-section">
            <h3>Your Bookings:</h3>
            <?php
            // Check if bookings are returned
            if (mysqli_num_rows($result_bookings) > 0) {
                echo '<table class="booking-table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Booking ID</th>';
                echo '<th>Bus No</th>';
                echo '<th>Route</th>';
                echo '<th>Price</th>';
                echo '<th>Phone</th>';
                echo '<th>Travel Date</th>';
                echo '<th>Number of People</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($booking = mysqli_fetch_assoc($result_bookings)) {
                    // Check if 'id' exists in the booking record instead of 'booking_id'
                    if (isset($booking['id'])) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($booking['id']) . '</td>'; 
                        echo '<td>' . htmlspecialchars($booking['bus_no']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['route']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['price']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['phone']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['travel_date']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['num_people']) . '</td>';
                        echo '</tr>';
                    } else {
                        // If 'id' is missing, display a fallback message
                        echo '<tr><td colspan="7">Booking ID not found in the record.</td></tr>';
                    }
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>You have no bookings.</p>';
            }
            ?>
        </div>

        <a href="./database/logout.php" class="back-btn">Log out</a>
    </div>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
