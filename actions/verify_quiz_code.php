<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to take a quiz'
    ]);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$quizCode = $data['quiz_code'] ?? '';

if (empty($quizCode)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a quiz code'
    ]);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kumidb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Check if quiz code exists
$stmt = $conn->prepare("SELECT quiz_id FROM Quizzes WHERE quiz_code = ?");
$stmt->bind_param("s", $quizCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'quiz_id' => $quiz['quiz_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid quiz code. Please check and try again.'
    ]);
}

$stmt->close();
$conn->close();
?> 