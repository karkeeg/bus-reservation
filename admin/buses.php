<?php
session_start();
require_once '../db_connect.php';

// Test database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../mainlogin/login.php');
    exit();
}

// Initialize error message
$error = '';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Delete Bus
    if (isset($_POST['delete_bus'])) {
        $bus_id = (int)$_POST['bus_id'];
        $sql = "DELETE FROM buses WHERE id = $bus_id";
        if($conn->query($sql)) {
            header('Location: buses.php');
            exit();
        }
    }
    
    // Handle Add/Edit Bus
    if (isset($_POST['save_bus'])) {
        // Get form data
        $bus_name = trim($_POST['bus_name'] ?? '');
        $from_city = trim($_POST['from_city'] ?? '');
        $to_city = trim($_POST['to_city'] ?? '');
        $departure_date = trim($_POST['departure_date'] ?? '');
        $seats = (int)($_POST['seats_available'] ?? 0);
        $ticket_price = (float)($_POST['ticket_price'] ?? 0);

        // Basic validation
        if (empty($bus_name) || empty($from_city) || empty($to_city) || empty($departure_date) || $seats <= 0 || $ticket_price <= 0) {
            $error = "Please fill all fields correctly";
        } else {
            try {
                if (isset($_POST['bus_id']) && !empty($_POST['bus_id'])) {
                    // Edit existing bus
                    $bus_id = (int)$_POST['bus_id'];
                    $stmt = $conn->prepare("UPDATE buses SET bus_name=?, from_city=?, to_city=?, departure_date=?, seats_available=?, ticket_price=? WHERE id=?");
                    $stmt->bind_param("ssssidi", $bus_name, $from_city, $to_city, $departure_date, $seats, $ticket_price, $bus_id);
                } else {
                    // Add new bus
                    $stmt = $conn->prepare("INSERT INTO buses (bus_name, from_city, to_city, departure_date, seats_available, ticket_price) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssid", $bus_name, $from_city, $to_city, $departure_date, $seats, $ticket_price);
                }
                
                if ($stmt->execute()) {
                    header("Location: buses.php");
                    exit();
                } else {
                    $error = "Error: " . $stmt->error;
                }
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Debug POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST Data: " . print_r($_POST, true));
}

// Fetch cities for dropdown
$cities_result = $conn->query("SELECT name FROM cities ORDER BY name");
$cityList = [];
while ($city = $cities_result->fetch_assoc()) {
    $cityList[] = $city['name'];
}

// Fetch all buses
$buses = $conn->query("SELECT * FROM buses ORDER BY departure_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background-color: #f1f5f9;
        }

        .sidebar {
            width: 250px;
            background: white;
            padding: 2rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100vh;
        }

        .logo {
            margin-bottom: 2rem;
            color: #2563eb;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav ul li {
            margin-bottom: 0.5rem;
        }

        .sidebar nav ul li a {
            display: block;
            padding: 0.75rem 1rem;
            color: #1e293b;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li.active a,
        .sidebar nav ul li a:hover {
            background-color: #2563eb;
            color: white;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .add-button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-button:hover {
            background-color: #1e40af;
        }

        .grid-item {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background-color: #f8fafc;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .actions button {
            padding: 0.3rem 0.8rem;
            margin: 0 0.2rem;
            border: none;
            border-radius: 0.3rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .actions button:first-child {
            background-color: #2563eb;
            color: white;
        }

        .delete-btn {
            background-color: #ef4444;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 0.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.3rem;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
        }

        .modal-content button[type="submit"] {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
            font-size: 1rem;
        }

        .error {
            color: red;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid red;
            background-color: #ffe6e6;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Bus Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="bookings.php">Bookings</a></li>
                    <li class="active"><a href="buses.php">Buses</a></li>
                    <li><a href="routes.php">Routes</a></li>
                    <li><a href="../mainlogin/logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>Bus Management</h1>
                <button class="add-button" onclick="showAddBusModal()">Add New Bus</button>
            </header>

            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="grid-item">
                <table>
                    <thead>
                        <tr>
                            <th>Bus Name</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure Date</th>
                            <th>Available Seats</th>
                            <th>Ticket Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($bus = $buses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                                <td><?php echo htmlspecialchars($bus['from_city']); ?></td>
                                <td><?php echo htmlspecialchars($bus['to_city']); ?></td>
                                <td><?php echo htmlspecialchars($bus['departure_date']); ?></td>
                                <td><?php echo htmlspecialchars($bus['seats_available']); ?></td>
                                <td><?php echo htmlspecialchars($bus['ticket_price']); ?></td>
                                <td class="actions">
                                    <button onclick="showEditBusModal(<?php echo htmlspecialchars(json_encode($bus)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this bus?');">
                                        <input type="hidden" name="bus_id" value="<?php echo $bus['id']; ?>">
                                        <button type="submit" name="delete_bus" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Bus Modal -->
    <div id="busModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Bus</h2>
            <form method="POST" action="" onsubmit="return validateForm()">
                <input type="hidden" id="bus_id" name="bus_id">
                
                <div class="form-group">
                    <label>Bus Name:</label>
                    <input type="text" name="bus_name" id="bus_name" required>
                </div>
                
                <div class="form-group">
                    <label>From City:</label>
                    <select name="from_city" id="from_city" required>
                        <option value="">Select departure city</option>
                        <?php foreach($cityList as $city): ?>
                            <option value="<?php echo htmlspecialchars($city); ?>">
                                <?php echo htmlspecialchars($city); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>To City:</label>
                    <select name="to_city" id="to_city" required>
                        <option value="">Select destination city</option>
                        <?php foreach($cityList as $city): ?>
                            <option value="<?php echo htmlspecialchars($city); ?>">
                                <?php echo htmlspecialchars($city); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Departure Date:</label>
                    <input type="date" name="departure_date" id="departure_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label>Available Seats:</label>
                    <input type="number" name="seats_available" id="seats_available" required min="1">
                </div>

                <div class="form-group">
                    <label>Ticket Price (Rs.):</label>
                    <input type="number" name="ticket_price" id="ticket_price" min="0" step="0.01" required>
                </div>

                <button type="submit" name="save_bus" class="btn btn-primary">Save Bus</button>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var fromCity = document.getElementById('from_city').value;
            var toCity = document.getElementById('to_city').value;
            var seats = document.getElementById('seats_available').value;
            var date = document.getElementById('departure_date').value;
            var price = document.getElementById('ticket_price').value;

            if (fromCity === toCity) {
                alert('Departure and destination cities cannot be the same!');
                return false;
            }

            if (seats < 1) {
                alert('Number of seats must be at least 1!');
                return false;
            }

            if (new Date(date) < new Date().setHours(0,0,0,0)) {
                alert('Departure date cannot be in the past!');
                return false;
            }

            if (price < 0) {
                alert('Ticket price cannot be negative!');
                return false;
            }

            return true;
        }

        function showAddBusModal() {
            document.getElementById('modalTitle').textContent = 'Add New Bus';
            document.getElementById('bus_id').value = '';
            document.getElementById('bus_name').value = '';
            document.getElementById('from_city').value = '';
            document.getElementById('to_city').value = '';
            document.getElementById('departure_date').value = '';
            document.getElementById('seats_available').value = '';
            document.getElementById('ticket_price').value = '';
            document.getElementById('busModal').style.display = 'block';
        }

        function showEditBusModal(bus) {
            document.getElementById('modalTitle').textContent = 'Edit Bus';
            document.getElementById('bus_id').value = bus.id;
            document.getElementById('bus_name').value = bus.bus_name;
            document.getElementById('from_city').value = bus.from_city;
            document.getElementById('to_city').value = bus.to_city;
            document.getElementById('departure_date').value = bus.departure_date;
            document.getElementById('seats_available').value = bus.seats_available;
            document.getElementById('ticket_price').value = bus.ticket_price;
            document.getElementById('busModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('busModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('busModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
