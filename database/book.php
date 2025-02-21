<?php
session_start();
require_once '../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch all cities for dropdown
$cities = [];
$cityQuery = "SELECT name FROM cities ORDER BY name";
$cityResult = $conn->query($cityQuery); 
while ($row = $cityResult->fetch_assoc()) {
    $cities[] = $row['name'];
}

// Initialize variables
$buses = [];
$message = "";

// Process the booking form submission (POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bus_id'])) {
    // Fetch the form data
    $bus_id = intval($_POST['bus_id']);
    $user_id = $_SESSION['user_id']; // User ID from session
    $num_passengers = intval($_POST['num_passengers']);
    $phone = $_POST['phone'];


    // Fetch ticket price from buses table
    $priceQuery = "SELECT ticket_price FROM buses WHERE bus_id = ?";
    if ($stmtPrice = $conn->prepare($priceQuery)) {
        $stmtPrice->bind_param("i", $bus_id);
        $stmtPrice->execute();
        $stmtPrice->bind_result($ticket_price);
        $stmtPrice->fetch();
        $stmtPrice->close();
    }
    $total_price = $ticket_price * $num_passengers;
    // Insert booking into the database
    // Fetch bus details (from_city, to_city, departure_date)
// Fetch bus details (bus_name, route, and travel_date)
// Fetch bus details (bus_name, route, and travel_date) from joined tables
$busQuery = "SELECT buses.bus_name, CONCAT(buses.from_city, ' - ', buses.to_city) AS route, buses.departure_date 
             FROM buses 
             WHERE buses.bus_id = ?";

if ($stmtBus = $conn->prepare($busQuery)) {
    $stmtBus->bind_param("i", $bus_id);
    $stmtBus->execute();
    $stmtBus->bind_result($bus_name, $route, $travel_date);
    $stmtBus->fetch();
    $stmtBus->close();
}

// Insert booking into the database with bus_name, route, and travel_date
$bookingQuery = "INSERT INTO bookings (bus_id, user_id, num_passengers, phone, ticket_price, bus_name, route, travel_date) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($bookingQuery)) {
    $stmt->bind_param("iiisisss", $bus_id, $user_id, $num_passengers, $phone, $total_price, $bus_name, $route, $travel_date);
    
    if ($stmt->execute()) {
        // Update available seats in buses table
        $updateSeatsQuery = "UPDATE buses SET seats_available = seats_available - ? WHERE bus_id = ?";
        if ($stmtUpdate = $conn->prepare($updateSeatsQuery)) {
            $stmtUpdate->bind_param("ii", $num_passengers, $bus_id);
            $stmtUpdate->execute();
        }

        // Redirect to avoid form resubmission
        header("Location: book.php?booking_success=true");
        exit();
    } else {
        $message = "Error while booking the bus. Please try again.";
    }
}

}

// Fetch all available buses (if no form submission or if the form is submitted with criteria)
$searchQuery = "SELECT * FROM buses WHERE seats_available > 0 AND departure_date >= NOW() ORDER BY departure_date ASC";;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from'])) {
    $from_city = $conn->real_escape_string($_POST['from']);
    $to_city = $conn->real_escape_string($_POST['to']);
    $travel_date = $conn->real_escape_string($_POST['date']);

    if (strtotime($travel_date) < strtotime(date('Y-m-d'))) {
        $message = "Please select a future date.";
    } else {
        if (!empty($from_city)) {
            $searchQuery .= " AND from_city = '$from_city'";
        }
        if (!empty($to_city)) {
            $searchQuery .= " AND to_city = '$to_city'";
        }
        if (!empty($travel_date)) {
            $searchQuery .= " AND departure_date = '$travel_date'";
        }
    }
}

// Execute the query to fetch buses
$result = $conn->query($searchQuery);
if ($result->num_rows > 0) {
    $buses = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "No buses available for the selected route and date.";
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

  select,
  input,
  button {
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .bus-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    margin-top: 15px;
    display: none;
    transition: all 0.3s ease-in-out;
  }

  .booking-form .form-group {
    margin-bottom: 15px;
  }

  .booking-form label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    color: #2c3e50;
  }

  .booking-form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
  }

  .booking-form input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
  }

  .btn-group {
    display: flex;
    gap: 10px;
  }

  .btn-success {
    background-color: #27ae60;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  .btn-success:hover {
    background-color: #219a52;
  }

  .btn-secondary {
    background-color: #95a5a6;
    color: white;
    margin-top:10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
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

        <?php if (isset($_GET['booking_success']) && $_GET['booking_success'] === 'true'): ?>
            <div class="message info">
                Booking successful! Your bus ticket has been reserved.
            </div>
        <?php endif; ?>

        <?php if (!empty($buses)): ?>
            <div class="bus-grid">
                <?php foreach ($buses as $bus): ?>
                    <div class="bus-card">
                        <div class="bus-info">
                            <h3><?php echo htmlspecialchars($bus['bus_name']); ?></h3>
                            <div class="price-tag">Rs. <?php echo number_format($bus['ticket_price'], 2); ?></div>
                            <p><strong>From:</strong> <?php echo htmlspecialchars($bus['from_city']); ?></p>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($bus['to_city']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($bus['departure_date'])); ?></p>
                            <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($bus['seats_available']); ?></p>
                        </div>

                        <button onclick="toggleBookingForm(<?php echo htmlspecialchars($bus['bus_id']); ?>)">Book Now</button>
                        <form id="form-<?php echo $bus['bus_id']; ?>" class="booking-form" method="POST">
                            <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                            <label>Number of Passengers</label>
                            <input type="number" name="num_passengers" required min="1" max="<?php echo $bus['seats_available']; ?>">
                            <label>Your Phone Number</label>
                            <input type="text" name="phone" min="10"  required>
                            <button type="submit" class="btn-success">Book Now</button>
                            <button type="button" class="btn-secondary" onclick="toggleBookingForm(<?php echo $bus['bus_id']; ?>)">Cancel</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
          function toggleBookingForm(busId) {
            const form = document.getElementById('form-' + busId);
            if (form.style.display === 'none' || !form.style.display) {
                document.querySelectorAll('.booking-form').forEach(f => f.style.display = 'none');
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>
</html>