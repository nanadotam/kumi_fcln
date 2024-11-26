<?php
session_start();

// Define the session timeout duration
define('SESSION_TIMEOUT', 900); // 15 minutes

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the last activity timestamp is set
    if (isset($_SESSION['last_activity'])) {
        // Calculate the session lifetime
        $session_lifetime = time() - $_SESSION['last_activity'];

        // If the session has expired, log the user out
        if ($session_lifetime > SESSION_TIMEOUT) {
            session_unset(); // Unset session variables
            session_destroy(); // Destroy the session
            header('Location: login.php?timeout=1'); // Redirect to login with a timeout message
            exit();
        }
    }

    // Update the last activity timestamp
    $_SESSION['last_activity'] = time();
}
?> 