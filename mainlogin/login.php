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
            header('Location: login.php?error=Invalid%20password');
        }
    } else {
        header('Location: login.php?error=Invalid%20email');
    }

    // Close connection
    $sql->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Ride-Mate</title>
    <style></style>
    <link rel="stylesheet" href="login.css" />
  </head>
  <body>
    <div class="login-container">
      <h1>Welcome Back!</h1>
      <p>Please log in to continue</p>

      <!-- Display error message dynamically -->
      <!-- <?php if (isset($_GET['error'])): ?>
      <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?> -->

      <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input
          type="password"
          name="password"
          placeholder="Password"
          required
        />
        <button type="submit">Log In</button>
      </form>

      <div class="links">
        <a href="forgotpassword.php">Forgot Password?</a>
        <a href="signup.php">Sign Up</a>
      </div>
    </div>

    <script>
      document
        .getElementById("login-form")
        .addEventListener("submit", function (event) {
          let isValid = true;

          // Validate email
          const email = document.getElementById("email");
          const emailError = document.getElementById("emailError");
          const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
          if (!emailRegex.test(email.value)) {
            emailError.textContent = "Please enter a valid email address.";
            isValid = false;
          } else {
            emailError.textContent = "";
          }

          // Validate password
          const password = document.getElementById("password");
          const passwordError = document.getElementById("passwordError");
          if (password.value.length < 6) {
            passwordError.textContent =
              "Password must be at least 6 characters long.";
            isValid = false;
          } else {
            passwordError.textContent = "";
          }

          // If validation fails, prevent form submission
          if (!isValid) {
            event.preventDefault();
          }
        });
    </script>
  </body>
</html>
