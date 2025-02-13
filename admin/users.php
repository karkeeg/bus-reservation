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
    // Handle Delete User
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            header('Location: users.php');
            exit();
        }
    }
    
    // Handle Add/Edit User
    if (isset($_POST['save_user'])) {
        $full_name = $conn->real_escape_string($_POST['full_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $gender = $conn->real_escape_string($_POST['gender']);
        $role = $conn->real_escape_string($_POST['role']);
        $dob = $conn->real_escape_string($_POST['dob']);

        try {
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                // Edit existing user
                $user_id = (int)$_POST['user_id'];
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone_number=?, gender=?, role=?, dob=?, password=? WHERE id=?");
                    $stmt->bind_param("sssssssi", $full_name, $email, $phone, $gender, $role, $dob, $password, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone_number=?, gender=?, role=?, dob=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $full_name, $email, $phone, $gender, $role, $dob, $user_id);
                }
            } else {
                // Add new user
                if (empty($_POST['password'])) {
                    $error = "Password is required for new users";
                } else {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, gender, password, role, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssss", $full_name, $email, $phone, $gender, $password, $role, $dob);
                }
            }

            if (isset($stmt) && $stmt->execute()) {
                header("Location: users.php");
                exit();
            } else if (isset($stmt)) {
                $error = "Error: " . $stmt->error;
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all users
$users = $conn->query("
    SELECT id, full_name, email, phone_number, gender, role, created_at, dob 
    FROM users 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Bus Reservation System</title>
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
            margin: 2% auto;
            padding: 2rem;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 800px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .close {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #2563eb;
        }

        .modal-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .password-toggle:hover {
            color: #2563eb;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
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
                    <li class="active"><a href="users.php">Users</a></li>
                    <li><a href="bookings.php">Bookings</a></li>
                    <li><a href="buses.php">Buses</a></li>
                    <li><a href="routes.php">Routes</a></li>
                    <li><a href="../mainlogin/logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>User Management</h1>
                <button class="add-button" onclick="showAddUserModal()">Add New User</button>
            </header>

            <div class="grid-item">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Role</th>
                                <th>Date of Birth</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                <td>
                                    <span class="role-<?php echo strtolower($user['role']); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['dob']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="showAddUserModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                        Edit
                                    </button>
                                    <?php if($user['role'] !== 'admin'): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="delete-btn">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title" id="modalTitle">Add New User</h2>
            
            <form method="POST" id="userForm">
                <input type="hidden" name="user_id" id="user_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required 
                               placeholder="Enter full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="Enter email address">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required 
                               placeholder="Enter 10-digit phone number" 
                               pattern="[0-9]{10}">
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width password-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" 
                               placeholder="Enter password">
                        <span class="password-toggle" onclick="togglePassword()">Show</span>
                        <small id="passwordHint">Password is required for new users</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddUserModal(user = null) {
            document.getElementById('modalTitle').textContent = user ? 'Edit User' : 'Add New User';
            document.getElementById('user_id').value = user ? user.id : '';
            document.getElementById('full_name').value = user ? user.full_name : '';
            document.getElementById('email').value = user ? user.email : '';
            document.getElementById('phone').value = user ? user.phone_number : '';
            document.getElementById('gender').value = user ? user.gender : '';
            document.getElementById('role').value = user ? user.role : '';
            document.getElementById('dob').value = user ? user.dob : '';
            
            const passwordField = document.getElementById('password');
            const passwordHint = document.getElementById('passwordHint');
            if (user) {
                passwordField.required = false;
                passwordHint.textContent = 'Leave blank to keep current password';
            } else {
                passwordField.required = true;
                passwordHint.textContent = 'Password is required for new users';
            }
            
            document.getElementById('userModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
            document.getElementById('userForm').reset();
        }

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleBtn.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                toggleBtn.textContent = 'Show';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('userModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
