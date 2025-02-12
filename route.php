<?php
include("db_connect.php"); // Include the header if necessary
session_start();

// Assuming user is logged in and their user_id is stored in session
$user_id = $_SESSION['user_id']; // Make sure user_id is set in session when the user logs in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Routes</title>
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Header Styling */
        header {
            text-align: center;
            background-color: #333;
            color: white;
            padding: 20px 0;
        }

        /* Container for all route cards */
        .route-container {
            display: flex;
            flex-direction: column; /* Stack cards vertically */
            align-items: center;
            margin-top: 20px;
            padding: 0 10px;
        }

        /* Styling for individual route cards */
        .route-card {
            display: flex; /* Align info and image side by side */
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 600px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            align-items: center; /* Center the content vertically */
        }

        /* Styling for route info (left side of the card) */
        .route-info {
            flex: 1;
        }

        /* Styling for the image (right side of the card) */
        .route-image img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
        }

        /* Button Styling */
        .book-now {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 20px;
            transition: background-color 0.3s;
        }

        /* Button hover effect */
        .book-now:hover {
            background-color: #218838;
        }

        /* Confirmation booking form (hidden by default) */
        .booking-form {
            display: none; /* Hidden initially */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container form input[type="submit"],
        .form-container form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-container form input[type="submit"] {
            background-color: #28a745;
            color: white;
        }

        .form-container form input[type="submit"]:hover {
            background-color: #218838;
        }

        .form-container form button {
            background-color: #dc3545;
            color: white;
        }

        .form-container form button:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>

<header>
    <h1>Available Bus Routes</h1>
</header>

<div class="route-container">
    <!-- Bus Route Card 1 -->
    <div class="route-card">
        <div class="route-info">
            <h2>Nepalgunj to Kathmandu</h2>
            <p><strong>Bus No:</strong> 101</p>
            <p><strong>Driver:</strong> Rajesh Sharma</p>
            <p><strong>Time:</strong> Everyday 7 AM</p>
        </div>
        <div class="route-image">
            <img src="images/bus1.jpg" alt="Bus Image">
        </div>
        <button class="book-now" onclick="openBookingForm('101', 'Nepalgunj to Kathmandu', 500)">Book Now</button>
    </div>

    <!-- Bus Route Card 2 -->
    <div class="route-card">
        <div class="route-info">
            <h2>Kathmandu to Surkhet</h2>
            <p><strong>Bus No:</strong> 102</p>
            <p><strong>Driver:</strong> Suman Thapa</p>
            <p><strong>Time:</strong> Everyday 7 AM</p>
        </div>
        <div class="route-image">
            <img src="/img/bus.jpeg" alt="Bus Image">
        </div>
        <button class="book-now" onclick="openBookingForm('102', 'Kathmandu to Surkhet', 700)">Book Now</button>
    </div>

    <!-- Bus Route Card 3 -->
    <div class="route-card">
        <div class="route-info">
            <h2>Kathmandu to Pokhara</h2>
            <p><strong>Bus No:</strong> 103</p>
            <p><strong>Driver:</strong> Binod Kumar</p>
            <p><strong>Time:</strong> Everyday 7 Am</p>
        </div>
        <div class="route-image">
            <img src="images/bus3.jpg" alt="Bus Image">
        </div>
        <button class="book-now" onclick="openBookingForm('103', 'Kathmandu to Pokhara', 600)">Book Now</button>
    </div>
</div>

<!-- Confirmation Booking Form (Hidden initially) -->
<div id="booking-form" class="booking-form">
    <div class="form-container">
        <h2>Confirm Booking</h2>
        <form action="confirm_booking.php" method="POST">
            <input type="hidden" id="bus_no" name="bus_no" value="">
            <input type="hidden" id="route" name="route" value="">
            <input type="hidden" id="Time" name="Time" value="">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>"> <!-- User ID -->

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="travel_date">Traveling Date:</label>
            <input type="date" id="travel_date" name="travel_date" required><br><br>

            <label for="num_people">Number of People:</label>
            <input type="number" id="num_people" name="num_people" required><br><br>

            <input type="submit" value="Confirm Booking">
            <button type="button" onclick="closeBookingForm()">Cancel</button>
        </form>
    </div>
</div>

<script>
    // Function to open the booking confirmation form
    function openBookingForm(busNo, route, Time) {
        document.getElementById("booking-form").style.display = "block";
        document.getElementById("bus_no").value = busNo;
        document.getElementById("route").value = route;
        document.getElementById("Time").value = Time;
    }

    // Function to close the booking confirmation form
    function closeBookingForm() {
        document.getElementById("booking-form").style.display = "none";
    }
</script>

</body>
</html>
