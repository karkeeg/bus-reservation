<?php
// signup.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Basic validations
    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // Password must contain at least one number
    if (!preg_match('/\d/', $password)) {
        die("Password must contain at least one number.");
    }

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'bus_reservation');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Email is already registered.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, dob, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullName, $email, $phone, $dob, $gender, $hashedPassword);

    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.php'>Login</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up | Ride-Mate</title>
    <style></style>
    <link rel="stylesheet" href="signup.css" />
  </head>
  <body>
    <div class="signup-container">
      <h2>SignUp</h2>

      <!-- Error message -->
      <div class="error-message">
        <!-- Display error messages here (if any) -->
        <span id="error-msg"></span>
      </div>

      <form id="signup-form" method="POST" action="signup.php">
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input
          type="tel"
          name="phone_number"
          placeholder="Phone Number "
          maxlength="10"
          required
        />
        <input type="date" name="dob" placeholder="Date of Birth" required />

        <select name="gender" required>
          <option value="" disabled selected>Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <input
          type="password"
          name="password"
          id="password"
          placeholder="Password"
          required
        />
        <input
          type="password"
          name="confirm_password"
          id="confirm_password"
          placeholder="Confirm Password"
          required
        />

        <button type="submit">Sign Up</button>
      </form>

      <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>

    <script>
      // Basic client-side validation
      document
        .getElementById("signup-form")
        .addEventListener("submit", function (e) {
          let errorMsg = "";
          const phone = document.querySelector('input[name="phone_number"]');
          const email = document.querySelector('input[name="email"]');
          const password = document.querySelector('input[name="password"]');
          const confirmPassword = document.querySelector(
            'input[name="confirm_password"]'
          );

          // Validate phone number (10 digits)
          if (phone.value.length !== 10) {
            errorMsg += "Phone number must be 10 digits. ";
          }

          // Validate email format (must be @gmail.com)
          if (!email.value.includes("@gmail.com")) {
            errorMsg += "Email must be a Gmail address. ";
          }

          // Validate password (at least one number)
          const passwordPattern = /^(?=.*\d)/;
          if (!password.value.match(passwordPattern)) {
            errorMsg += "Password must contain at least one number. ";
          }

          // Confirm password must match
          if (password.value !== confirmPassword.value) {
            errorMsg += "Passwords do not match. ";
          }

          // If there are any error messages, prevent form submission
          if (errorMsg) {
            e.preventDefault();
            document.getElementById("error-msg").textContent = errorMsg;
          }
        });
    </script>
  </body>
</html>
