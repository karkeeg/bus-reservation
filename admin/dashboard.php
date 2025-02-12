<?php
session_start();


// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch statistics for total users and total bookings
$totalUsersQuery = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $totalUsersQuery->fetch_assoc()['total_users'];

$totalBookingsQuery = $conn->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$totalBookings = $totalBookingsQuery->fetch_assoc()['total_bookings'];

// Fetch recent bookings
// $recentBookingsQuery = $conn->query("
//     SELECT b.id, b.user_id, b.bus_no, 
//            CONCAT(bu.from_city, ' to ', bu.to_city) AS route, 
//            b.num_people, b.price, b.travel_date 
//     FROM bookings b
//     JOIN buses bu ON b.bus_no = bu.id
//     ORDER BY b.travel_date DESC LIMIT 5
// ");


// Fetch statistics for male and female users
$totalMaleUsersQuery = $conn->query("SELECT COUNT(*) AS male_users FROM users WHERE gender = 'Male'");
$totalMaleUsers = $totalMaleUsersQuery->fetch_assoc()['male_users'];

$totalFemaleUsersQuery = $conn->query("SELECT COUNT(*) AS female_users FROM users WHERE gender = 'Female'");
$totalFemaleUsers = $totalFemaleUsersQuery->fetch_assoc()['female_users'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="dasahboard.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Manage Buses</a>
        <a href="#">Manage Bookings</a>
        <a href="#">User Management</a>
        <a href="#">Settings</a>
        <div class="add-user-btn" onclick="openModal()">Add New User</div>
    </div>

    <div class="dashboard-content">
        <div class="navbar">
            <h1>Welcome, Admin</h1>
            <a href="../database/logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="cards">
            <div class="card">
                <div class="icon">🚌</div>
                <h3>Total Buses</h3>
                <p><?php echo $conn->query("SELECT COUNT(*) AS total_buses FROM buses")->fetch_assoc()['total_buses']; ?></p>
            </div>
            <div class="card">
                <div class="icon">📅</div>
                <h3>Total Bookings</h3>
                <p><?php echo $totalBookings; ?></p>
            </div>
            <div class="card">
                <div class="icon">👥</div>
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="card">
                <div class="icon">👨/👩</div>
                <h3>Male/Female Users</h3>
                <p><?php echo "$totalMaleUsers / $totalFemaleUsers"; ?></p>
            </div>
        </div>

        <div class="table-container">
            <h2>Recent Bookings</h2>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Bus</th>
                    <th>Route</th>
                    <th>Travel Date</th>
                    <th>Persons</th>
                    <th>Total Price</th>
                </tr>
                <?php
                while ($booking = $recentBookingsQuery->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>#{$booking['id']}</td>";
                    echo "<td>{$booking['user_id']}</td>";
                    echo "<td>{$booking['bus_no']}</td>";
                    echo "<td>{$booking['route']}</td>";
                    echo "<td>{$booking['travel_date']}</td>";
                    echo "<td>{$booking['num_people']}</td>";
                    echo "<td>{$booking['price']}</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Modal for Add User -->
     
    <div class="modal" id="userModal">
    
        <div class="modal-content"><h1>Add New User</h1>
            <span class="close" onclick="closeModal()">&times;</span>
            <form action="adduser.php" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="phone" name="phone_number" placeholder="Phone Number" required> <!-- updated to phone_number -->
                <input type="date" name="dob" placeholder="Date of Birth" required> <!-- new dob field -->
                <select name="gender" required> <!-- new gender field -->
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button type="submit">Add User</button>
            </form>

        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('userModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('userModal').style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('userModal')) {
                closeModal();
            }
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>