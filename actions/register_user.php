<?php
session_start();
require_once '../db/config.php';
require_once '../functions/auth_functions.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get and sanitize inputs from URL-encoded data
        $first_name = trim($_POST['first_Name']);
        $last_name = trim($_POST['last_Name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = strtolower(trim($_POST['role']));

        // Validate input
        $errors = validateRegistrationData($email, $password, $first_name, $last_name);

        // Validate role
        if (!in_array($role, ['student', 'teacher'])) {
            $errors[] = 'Invalid role selected.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => $errors[0]
            ]);
            exit();
        }

        // Check if email exists
        if (checkEmailExists($conn, $email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists.'
            ]);
            exit();
        }

        // Register user
        if (registerUser($conn, $first_name, $last_name, $email, $password, $role)) {
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful! Redirecting to login...'
            ]);
        } else {
            throw new Exception("Registration failed");
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during registration. Please try again.'
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
