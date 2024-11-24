<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "bus_reservation"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, password, full_name FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword, $userName);

        if ($stmt->fetch()) {
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['userId'] = $userId;
                $_SESSION['userName'] = $userName;
                $_SESSION['message'] = "Login successfully!"; // Store the message
                header("Location: ../availablebus/availablebus.html"); // Redirect to the homepage
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email address.";
        }
        $stmt->close();
    } else {
        $error = "Error in the database query.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="../login/./login.css" />
</head>
<body>
    <!-- Header section -->
    <header>
      <div class="logo">
        <a href="../index.html"><img src="../img/logo.png" alt="Logo" /></a>
      </div>

      <nav class="nav-links">
        <a href="../index.html">Home</a>
        <a href="#">Available Bus</a>
        <a href="#">Our Services</a>
        <a href="#">Contact Us</a>
      </nav>
      <div class="auth-section">
        <a href="../login/login.html"><button>Login</button></a>
        <a href="../signup/sign.html"><button>Register</button></a>
        <div class="profile" id="profile">
          <a href="/introcard/introcard.html"
            ><img src="../img/bibek.jpg" alt="Profile"
          /></a>
        </div>
      </div>
    </header>

    <!-- Login Form Section -->
    <section>
      <div class="login-container">
        <h2>Login</h2>
        <form id="loginForm" action="login.php" method="POST">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              required
            />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              minlength="6"
              required
            />
          </div>
          <div class="btn-container">
            <button type="submit" class="btn">Login</button>
          </div>
        </form>
        <div class="forgot-password">
          <a href="#">Forgot Password?</a>
        </div>
        <?php if (isset($error)) { echo "<p class='error' style='color:white;' >$error</p>"; } ?>
      </div>
    </section>

    <!-- Footer Section -->
    <footer>
      <div class="content">
        <div class="left-box">
          <div class="upper">
            <div class="topic">About Us</div>
            <p>
              Ride-Mate is a Transportation booking system Where people can book
              as per their satisfaction and preferences.
            </p>
          </div>
          <div class="lower">
            <div class="topic">Contact Us</div>
            <div class="phone">
              <a href="#"><i>📞</i>+977-9860917585</a>
            </div>
            <div class="email">
              <a href="#"><i>✉️</i>abc@gmail.com</a>
            </div>
          </div>
        </div>
        <div class="middle-box">
          <div class="topic">Our Services</div>
          <div><a href="#">Responsive Design</a></div>
          <div><a href="#">Easy Routes</a></div>
          <div><a href="#">Preference booking</a></div>
          <div><a href="#">Experienced Services</a></div>
          <div><a href="#">Service Providers</a></div>
          <div><a href="#">Customers Feedback</a></div>
        </div>
        <div class="right-box">
          <div class="topic">Connect Us</div>
          <form action="#"></form>
            <input type="text" placeholder="Enter email" />
            <input type="submit" value="Send" />
            <div class="media-icons">
              <a href="#" class="F-icons">F</a>
              <a href="#" class="I-icons">I</a>
              <a href="#" class="X-icons">X</a>
              <a href="#" class="Y-icons">Y</a>
              <a href="#" class="In-icons">in</a>
            </div>
          </form>
        </div>
        <div class="bottom">
          <p>
            Copyright &#169; 2024 <a href="#">Ride-Mate </a>All right reserved
          </p>
        </div>
      </div>
    </footer>
</body>
</html>
