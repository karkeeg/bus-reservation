<?php
session_start();
require_once '../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Initialize variables
$buses = [];
$message = "";
$cities = [];

// Get all cities for dropdowns
$cityQuery = "SELECT name FROM cities ORDER BY name";
$cityResult = $conn->query($cityQuery);
while ($row = $cityResult->fetch_assoc()) {
    $cities[] = $row['name'];
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_city = $conn->real_escape_string($_POST['from']);
    $to_city = $conn->real_escape_string($_POST['to']);
    $travel_date = $conn->real_escape_string($_POST['date']);

    if (strtotime($travel_date) < strtotime(date('Y-m-d'))) {
        $message = "Please select a future date.";
    } else {
        // Fetch available buses with fare information
        $sql = "SELECT b.*
                FROM buses b 
                WHERE b.departure_date = ? 
                AND b.from_city = ? 
                AND b.to_city = ? 
                AND b.seats_available > 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $travel_date, $from_city, $to_city);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $buses = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $message = "No buses available for the selected route and date.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Bus Tickets</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .search-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        select, input, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .message.error {
            background-color: #fee;
            color: #c0392b;
            border: 1px solid #e74c3c;
        }

        .message.info {
            background-color: #eef;
            color: #2980b9;
            border: 1px solid #3498db;
        }

        .bus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .bus-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .bus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .bus-info {
            margin-bottom: 15px;
        }

        .bus-name {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .price-tag {
            background: #27ae60;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .booking-form {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
            display: none;
        }

        .booking-form input {
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #219a52;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .validation-error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Your Bus Ticket</h1>
        
        <form method="post" class="search-form">
            <div class="form-row">
                <div class="form-group">
                    <label>From</label>
                    <select name="from" required>
                        <option value="">Select departure city</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo htmlspecialchars($city); ?>" 
                                <?php echo (isset($_POST['from']) && $_POST['from'] == $city) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>To</label>
                    <select name="to" required>
                        <option value="">Select destination city</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo htmlspecialchars($city); ?>"
                                <?php echo (isset($_POST['to']) && $_POST['to'] == $city) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Travel Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit">Search Buses</button>
                </div>
            </div>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'No buses') !== false ? 'info' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($buses)): ?>
            <div class="bus-grid">
                <?php foreach ($buses as $bus): ?>
                    <div class="bus-card">
                        <div class="bus-info">
                            <h3 class="bus-name"><?php echo htmlspecialchars($bus['bus_name']); ?></h3>
                            <div class="price-tag">Rs. <?php echo number_format($bus['ticket_price'], 2); ?></div>
                            <p><strong>From:</strong> <?php echo htmlspecialchars($bus['from_city']); ?></p>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($bus['to_city']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($bus['departure_date'])); ?></p>
                            <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($bus['seats_available']); ?></p>
                        </div>
                        
                        <button onclick="toggleBookingForm(<?php echo htmlspecialchars($bus['id']); ?>)">
                            Book Now
                        </button>

                        <div id="form-<?php echo htmlspecialchars($bus['id']); ?>" class="booking-form">
                            <form method="post" action="../confirm_booking.php" onsubmit="return validateBookingForm(this)">
                                <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus['id']); ?>">
                                <input type="hidden" name="date" value="<?php echo htmlspecialchars($bus['departure_date']); ?>">
                                <input type="hidden" name="fare" value="<?php echo htmlspecialchars($bus['ticket_price']); ?>">
                                
                                <div class="form-group">
                                    <label>Number of Passengers</label>
                                    <input type="number" name="num_passengers" required min="1" 
                                           max="<?php echo htmlspecialchars($bus['seats_available']); ?>">
                                    <div class="validation-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" required pattern="[0-9]{10}" 
                                           title="Please enter a valid 10-digit phone number">
                                    <div class="validation-error"></div>
                                </div>
                                
                                <div class="btn-group">
                                    <button type="submit" class="btn-success">Confirm Booking</button>
                                    <button type="button" class="btn-secondary" 
                                            onclick="toggleBookingForm(<?php echo htmlspecialchars($bus['id']); ?>)">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleBookingForm(busId) {
            const form = document.getElementById('form-' + busId);
            if (form.style.display === 'none' || !form.style.display) {
                // Hide all other forms
                document.querySelectorAll('.booking-form').forEach(f => f.style.display = 'none');
                // Show the selected form
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        function validateBookingForm(form) {
            let isValid = true;
            const numPassengers = form.querySelector('input[name="num_passengers"]');
            const phone = form.querySelector('input[name="phone"]');
            
            // Validate number of passengers
            if (numPassengers.value < 1 || numPassengers.value > parseInt(numPassengers.max)) {
                numPassengers.nextElementSibling.textContent = `Please enter a number between 1 and ${numPassengers.max}`;
                isValid = false;
            } else {
                numPassengers.nextElementSibling.textContent = '';
            }

            // Validate phone number
            if (!phone.value.match(/^[0-9]{10}$/)) {
                phone.nextElementSibling.textContent = 'Please enter a valid 10-digit phone number';
                isValid = false;
            } else {
                phone.nextElementSibling.textContent = '';
            }

            return isValid;
        }

        // Set minimum date for date input
        document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
