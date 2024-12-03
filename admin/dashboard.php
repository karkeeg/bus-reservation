<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="?page=dashboard">Dashboard</a>
        <a href="?page=managebus">Manage Buses</a>
        <a href="?page=managebookings">Manage Bookings</a>
        <a href="?page=usermanagement">User Management</a>
        <a href="?page=settings">Settings</a>
        <!-- Add New User Button -->
        <div class="add-user-btn" onclick="openModal()">Add New User</div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content">
        <?php
            // Dynamically load content based on the "page" parameter
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

            // Validate and include the requested page
            $allowed_pages = ['dashboard', 'managebus', 'managebookings', 'usermanagement', 'settings'];
            if (in_array($page, $allowed_pages)) {
                include $page . '.php';
            } else {
                echo "<h1>Page not found</h1>";
            }
        ?>
    </div>

    <!-- Modal for Add User -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form action="adduser.php" method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
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
            document.getElementById("userModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("userModal").style.display = "none";
        }

        // Close the modal if the user clicks outside it
        window.onclick = function (event) {
            if (event.target == document.getElementById("userModal")) {
                closeModal();
            }
        };
    </script>
</body>
</html>
