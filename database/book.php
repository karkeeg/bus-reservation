<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bus_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$buses = [];
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_date = $_POST['date'];
    $from_city = $_POST['from'];  // Add from_city
    $to_city = $_POST['to'];      // Add to_city

    // Fetch available buses for the selected date and route
    $sql = "SELECT * FROM buses WHERE departure_date = ? AND from_city = ? AND to_city = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $selected_date, $from_city, $to_city);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $buses = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = "No buses available for the selected date and route.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Buses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label, input, button {
            display: block;
            margin: 10px 0;
        }
        input, button {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: calc(33.333% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .card img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .hidden {
            display: none;
        }
        .cancel-button {
            background-color: #f44336;
            color: white;
        }
        .cancel-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    
    <?php if (!empty($buses)): ?>
        <h3>Available Buses for <?php echo htmlspecialchars($selected_date); ?> from <?php echo htmlspecialchars($from_city); ?> to <?php echo htmlspecialchars($to_city); ?></h3>
        <div class="card-container">
            <?php foreach ($buses as $bus): ?>
                <?php
                // Get the day of the week
                $day_of_week = date('l', strtotime($bus['departure_date']));
                ?>
                <div class="card" onclick="toggleBookingForm(<?php echo htmlspecialchars($bus['id']); ?>)">
                   <h3><?php echo htmlspecialchars($bus['bus_name']); ?></h3>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($bus['from_city']); ?> to <?php echo htmlspecialchars($bus['to_city']); ?></p>
                    <p><strong>Fare:</strong> Rs. <?php echo htmlspecialchars($bus['fare']); ?></p>
                    <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($bus['seats_available']); ?></p>
                    <p><strong>Departure Date:</strong> <?php echo htmlspecialchars($bus['departure_date']); ?> (<?php echo $day_of_week; ?>)</p>
                </div>
                <div id="form-<?php echo htmlspecialchars($bus['id']); ?>" class="hidden">
                    <form method="post" action="confirm_booking.php" onsubmit="return validateForm()">
                        <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus['id']); ?>">
                        <input type="hidden" name="date" value="<?php echo htmlspecialchars($bus['departure_date']); ?>">
                        
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" placeholder="Enter your full name">
                        
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required pattern="^[0-9]{10}$" title="Phone number must be 10 digits" placeholder="Enter your phone number">
                        
                        <button type="submit">Book Now</button>
                        <button type="button" class="cancel-button" onclick="toggleBookingForm(<?php echo htmlspecialchars($bus['id']); ?>)">Cancel</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <script>
        function toggleBookingForm(busId) {
            const form = document.getElementById('form-' + busId);
            if (form.classList.contains('hidden')) {
                // Hide all other forms
                document.querySelectorAll('.hidden').forEach(f => f.classList.add('hidden'));
                // Show the selected form
                form.classList.remove('hidden');
            } else {
                // Hide the form if already visible
                form.classList.add('hidden');
            }
        }

        function validateForm() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;

            // Name validation: only letters and spaces allowed
            const namePattern = /^[A-Za-z\s]+$/;
            if (!namePattern.test(name)) {
                alert("Please enter a valid name (only letters and spaces are allowed).");
                return false;
            }

            // Phone validation: must be a 10-digit number
            const phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phone)) {
                alert("Please enter a valid 10-digit phone number.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
