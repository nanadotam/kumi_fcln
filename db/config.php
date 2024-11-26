<?php
// Start a new session if one doesn't exist already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// // Database connection credentials
// $servername = "localhost";  // Database host
// $username = "nana.amoako";         // Database username
// $password = "6YM7YABR!b9";            // Database password (empty for local development)
// $dbname = "webtech_fall2024_nana_amoako";        // Name of the database


// Database connection credentials      - LOCAL
$servername = "localhost";  // Database host
$username = "root";         // Database username
$password = "";            // Database password (empty for local development)
$dbname = "webtech_fall2024_nana_amoako";        // Name of the database

// Create new MySQL database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    // If connection fails, terminate and show error
    die("Connection failed: " . $conn->connect_error);
} else {
    // If connection succeeds, set success notification
    $_SESSION['notification'] = "Connection successful";
}

// Return the database connection object
return $conn;
?>