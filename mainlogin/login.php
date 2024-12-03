<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Change from username to email
    $password = $_POST['password'];

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
            $_SESSION['username'] = $user['full_name']; // Store full name in session (optional)
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/index.html'); // Admin dashboard
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
