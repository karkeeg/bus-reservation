<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: mainlogin/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    header('Location: mainlogin/login.php');
    exit();
}

// Fetch user's bookings
$sql_bookings = "SELECT b.*, bs.from_city, bs.to_city 
                 FROM bookings b 
                 LEFT JOIN buses bs ON b.bus_no = bs.bus_name 
                 WHERE b.user_id = ? 
                 ORDER BY b.travel_date DESC";
$stmt = $conn->prepare($sql_bookings);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Bus Reservation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: #4a90e2;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .profile-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .profile-content {
            padding: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .info-item strong {
            color: #4a90e2;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .bookings-section {
            margin-top: 30px;
        }

        .bookings-section h2 {
            color: #4a90e2;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .booking-table th,
        .booking-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .booking-table th {
            background: #4a90e2;
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
        }

        .booking-table tr:hover {
            background: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4a90e2;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #357abd;
        }

        .btn-secondary {
            background: #f0f2f5;
            color: #1a1a1a;
        }

        .btn-secondary:hover {
            background: #e4e6e9;
        }

        .btn-danger {
            background: #dc3545;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .booking-table {
                display: block;
                overflow-x: auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="profile-header">
                <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
            </div>

            <div class="profile-content">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Full Name</strong>
                        <?php echo htmlspecialchars($user['full_name']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Email</strong>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Phone</strong>
                        <?php echo htmlspecialchars($user['phone_number']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Gender</strong>
                        <?php echo htmlspecialchars($user['gender']); ?>
                    </div>
                </div>

                <div class="bookings-section">
                    <h2>My Bookings</h2>
                    <?php if ($result_bookings->num_rows > 0): ?>
                        <table class="booking-table">
                            <thead>
                                <tr>
                                    <th>Bus No</th>
                                    <th>Route</th>
                                    <th>Travel Date</th>
                                    <th>Seats</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $result_bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['bus_no']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['from_city']) . ' to ' . htmlspecialchars($booking['to_city']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['travel_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($booking['num_people']); ?></td>
                                        <td>Rs. <?php echo number_format($booking['price'], 2); ?></td>
                                        <td>
                                            <?php if (strtotime($booking['travel_date']) > time()): ?>
                                                <form action="cancel_booking.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <button type="submit" class="btn btn-danger">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: #6c757d;">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't made any bookings yet.</p>
                    <?php endif; ?>
                </div>

                <div class="action-buttons">
                    <a href="landingpage.html" class="btn btn-secondary">Back to Home</a>
                    <a href="mainlogin/logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
