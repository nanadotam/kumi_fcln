<?php
session_start();
require_once '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The form field names (firstName, lastName) come from the HTML form in register.php
    // While the database columns use first_name, last_name
    // The names don't need to match exactly - we just need to map them correctly when inserting
    $firstName = trim($_POST['firstName']); // Maps to first_name in Users table
    $lastName = trim($_POST['lastName']);   // Maps to last_name in Users table
    $email = trim($_POST['email']);
    $password = $_POST['password']; 
    $role = trim($_POST['role']);

    // Validate inputs (add your validation logic here)

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO Users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $_SESSION['notification'] = "Registration successful! You can now log in.";
        header("Location: ../view/login.php");
    } else {
        $_SESSION['notification'] = "Registration failed: " . $stmt->error;
        header("Location: ../view/register.php");
    }

    $stmt->close();
    $conn->close();
}
?>
