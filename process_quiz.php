<?php
header('Content-Type: application/json');
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kumidb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Please log in first"]);
    exit;
}

// Insert quiz data
$title = $_POST['title'];
$description = $_POST['description'];
$created_by = $_SESSION['user_id'];
$mode = $_POST['mode'];
$deadline = $_POST['deadline'];
$shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
$shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
$max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
$time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

// Create a prepared statement
$stmt = $conn->prepare("INSERT INTO Quizzes (title, description, created_by, mode, deadline, 
        shuffle_questions, shuffle_answers, max_attempts, time_limit) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Bind parameters
$stmt->bind_param("ssissiiis", $title, $description, $created_by, $mode, $deadline, 
                  $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit);

// Execute the statement
if ($stmt->execute()) {
    $quiz_id = $conn->insert_id;
    $success = true;
    
    // Insert questions and answers
    foreach ($_POST['questions'] as $question) {
        // ... existing question insertion code ...
    }
    
    echo json_encode(['success' => true, 'message' => 'New quiz created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 