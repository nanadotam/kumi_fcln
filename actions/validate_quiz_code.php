<?php
session_start();
require_once '../utils/Database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$quizCode = trim($_POST['quizcode'] ?? '');

if (empty($quizCode)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a quiz code']);
    exit();
}

try {
    $db = Database::getInstance();
    
    // Check if quiz code exists and quiz is active
    $sql = "SELECT quiz_id FROM Quizzes 
            WHERE quiz_code = ? 
            AND (deadline IS NULL OR deadline > NOW())";
            
    $result = $db->query($sql, [$quizCode]);
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid quiz code. Please try again.']);
        exit();
    }
    
    $quiz = $result->fetch_assoc();
    echo json_encode([
        'success' => true, 
        'redirect' => 'take_quiz.php?id=' . $quiz['quiz_id']
    ]);
    
} catch (Exception $e) {
    error_log("Quiz code validation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}