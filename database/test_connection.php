<?php
include '../db_connect.php';  // Correct path to include db_connect.php (if it's in the root folder)

// Check if the connection was successful
if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed!";
}

// Close the connection
$conn->close();
?>
