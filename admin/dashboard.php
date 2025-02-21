<?php
session_start();
require_once '../db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../mainlogin/login.php');
    exit();
}

// Fetch statistics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$totalBookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$totalBuses = $conn->query("SELECT COUNT(*) as count FROM buses")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(ticket_price) as total FROM bookings")->fetch_assoc()['total'];

// Fetch recent bookings
$recentBookings = $conn->query("
    SELECT b.*, u.full_name 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.id DESC 
    LIMIT 5
");

// Fetch available buses
$availableBuses = $conn->query("
    SELECT * FROM buses 
    WHERE departure_date >= CURDATE() 
    ORDER BY departure_date 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bus Reservation System</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Bus Admin</h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="bookings.php">Bookings</a></li>
                    <li><a href="buses.php">Buses</a></li>
                    <li><a href="routes.php">Routes</a></li>
                    <li><a href="../mainlogin/logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>Dashboard Overview</h1>
                <div class="user-info">
                    <span>Welcome, Admin</span>
                </div>
            </header>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <p><?php echo $totalBookings; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Available Buses</h3>
                    <p><?php echo $totalBuses; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p>Rs. <?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="grid-item">
                    <h2>Recent Bookings</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Passenger</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = $recentBookings->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['route']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                                    <td>Rs. <?php echo number_format($booking['ticket_price'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid-item">
                    <h2>Available Buses</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bus Name</th>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($bus = $availableBuses->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                                    <td><?php echo htmlspecialchars($bus['from_city']) . ' - '  . htmlspecialchars($bus['to_city']); ?></td>
                                    <td><?php echo htmlspecialchars($bus['departure_date']); ?></td>
                                    <td><?php echo htmlspecialchars($bus['seats_available']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>

<?php
$conn->close();
?>