<?php
session_start(); // Start session to manage login state
$error = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'bus_reservation');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL query to prevent SQL injection
    $sql = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username']; // Set session variable
            header('Location: ../index.html'); // Redirect to dashboard
            exit();
        } else {
            $error = 'Invalid password.'; // Set error message for incorrect password
        }
    } else {
        $error = 'Invalid username.'; // Set error message for incorrect username
    }

    // Close database connection
    $sql->close();
    $conn->close();
}
?>
