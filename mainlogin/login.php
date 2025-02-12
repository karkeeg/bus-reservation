<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate email and password input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login.html?error=Invalid%20email');
        exit();
    }

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'bus_reservation');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query to fetch user by email
    $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id']; // Store user ID
            $_SESSION['username'] = $user['full_name']; // Store full name
            $_SESSION['role'] = $user['role']; // Store role

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php'); // Admin dashboard
            } else {
                header('Location: ../landingpage.html'); // User dashboard
            }
            exit();
        } else {
            header('Location: login.html?error=Invalid%20password');
        }
    } else {
        header('Location: login.html?error=Invalid%20email');
    }

    // Close connection
    $sql->close();
    $conn->close();
}
?>
