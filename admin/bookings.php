<?php
session_start();
require_once '../db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../mainlogin/login.php');
    exit();
}

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $old_status = $conn->real_escape_string($_POST['old_status']);
    
    // If cancelling a confirmed booking, update bus seats
    if ($old_status === 'confirmed' && $status === 'cancelled') {
        $booking_query = $conn->query("SELECT bus_no, num_people FROM bookings WHERE id = $booking_id");
        if ($booking = $booking_query->fetch_assoc()) {
            $bus_no = $booking['bus_no'];
            $seats = $booking['num_people'];
            $conn->query("UPDATE buses SET seats_available = seats_available + $seats WHERE bus_name = '$bus_name'");
        }
    }
    // If confirming a cancelled booking, update bus seats
    else if ($old_status === 'cancelled' && $status === 'confirmed') {
        $booking_query = $conn->query("SELECT bus_id, num_passengers FROM bookings WHERE id = $booking_id");
        if ($booking = $booking_query->fetch_assoc()) {
            $bus_no = $booking['bus_id'];
            $seats = $booking['num_passengers'];
            $conn->query("UPDATE buses SET seats_available = seats_available - $seats WHERE bus_name = '$bus_name'");
        }
    }
    
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $booking_id");
    header('Location: bookings.php');
    exit();
}

// Fetch all bookings with user details
$bookings = $conn->query("
    SELECT b.*, u.full_name, u.email
    FROM bookings b 
    JOIN users u ON b.user_id = u.id
    ORDER BY b.travel_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management - Bus Reservation System</title>
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
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border: none;
            border-radius: 0.3rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #2563eb;
            color: white;
        }

        .actions button:hover {
            background-color: #1e40af;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
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
            max-width: 500px;
            margin: 5% auto;
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        .booking-details {
            background-color: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }

        .booking-details p {
            margin: 0.5rem 0;
            color: #374151;
        }

        .booking-details strong {
            color: #1f2937;
        }

        .status-options {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }

        .status-option {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .status-option:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
        }

        .status-option.selected {
            border-color: #2563eb;
            background-color: #eff6ff;
        }

        .status-option.confirm {
            border-color: #dcfce7;
        }

        .status-option.cancel {
            border-color: #fee2e2;
        }

        .status-option.confirm.selected {
            background-color: #dcfce7;
            border-color: #166534;
        }

        .status-option.cancel.selected {
            background-color: #fee2e2;
            border-color: #991b1b;
        }

        .modal-footer {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .refund-info {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #fee2e2;
            border-radius: 0.5rem;
            color: #991b1b;
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

            .status-options {
                flex-direction: column;
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
                    <li class="active"><a href="bookings.php">Bookings</a></li>
                    <li><a href="buses.php">Buses</a></li>
                    <li><a href="routes.php">Routes</a></li>
                    <li><a href="../mainlogin/logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>Booking Management</h1>
            </header>

            <div class="grid-item">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Passenger</th>
                                <th>Bus No</th>
                                <th>Route</th>
                                <th>Travel Date</th>
                                <th>People</th>
                                <th>Price</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['bus_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['route']); ?></td>
                                <td><?php echo htmlspecialchars($booking['travel_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['num_passengers']); ?></td>
                                <td>Rs. <?php echo number_format($booking['ticket_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['status'] ?? 'pending'; ?>">
                                        <?php echo ucfirst($booking['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button onclick="showStatusModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title">Update Booking Status</h2>
            <form method="POST" id="statusForm">
                <input type="hidden" name="booking_id" id="booking_id">
                <input type="hidden" name="old_status" id="old_status">
                
                <div class="booking-details" id="bookingDetails">
                    <!-- Booking details will be inserted here by JavaScript -->
                </div>

                <div class="status-options">
                    <div class="status-option" data-status="pending" onclick="selectStatus('pending')">
                        <h3>Pending</h3>
                        <p>Awaiting confirmation</p>
                    </div>
                    <div class="status-option confirm" data-status="confirmed" onclick="selectStatus('confirmed')">
                        <h3>Confirm</h3>
                        <p>Approve booking</p>
                    </div>
                    <div class="status-option cancel" data-status="cancelled" onclick="selectStatus('cancelled')">
                        <h3>Cancel</h3>
                        <p>Cancel booking</p>
                    </div>
                </div>

                <div id="refundInfo" class="refund-info" style="display: none;">
                    Note: Cancelling a confirmed booking will return the seats to the bus inventory.
                </div>

                <input type="hidden" name="status" id="selected_status">
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showStatusModal(booking) {
            document.getElementById('booking_id').value = booking.id;
            document.getElementById('old_status').value = booking.status || 'pending';
            
            // Update booking details
            const details = `
                <p><strong>Booking ID:</strong> #${booking.id}</p>
                <p><strong>Passenger:</strong> ${booking.full_name}</p>
                <p><strong>Bus No:</strong> ${booking.bus_no}</p>
                <p><strong>Route:</strong> ${booking.route}</p>
                <p><strong>Travel Date:</strong> ${booking.travel_date}</p>
                <p><strong>Number of People:</strong> ${booking.num_people}</p>
                <p><strong>Total Price:</strong> Rs. ${parseFloat(booking.price).toFixed(2)}</p>
                <p><strong>Phone:</strong> ${booking.phone}</p>
            `;
            document.getElementById('bookingDetails').innerHTML = details;
            
            // Select current status
            selectStatus(booking.status || 'pending');
            
            document.getElementById('statusModal').style.display = 'block';
        }

        function selectStatus(status) {
            // Remove selected class from all options
            document.querySelectorAll('.status-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            document.querySelector(`.status-option[data-status="${status}"]`).classList.add('selected');
            
            // Update hidden input
            document.getElementById('selected_status').value = status;
            
            // Show/hide refund info
            const oldStatus = document.getElementById('old_status').value;
            document.getElementById('refundInfo').style.display = 
                (oldStatus === 'confirmed' && status === 'cancelled') ? 'block' : 'none';
        }

        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
            document.getElementById('statusForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('statusModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
