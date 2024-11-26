<?php
session_start();
require_once '../utils/Database.php';
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

try {
    $db = Database::getInstance();
    
    // Check if quiz code exists
    $sql = "SELECT quiz_id FROM Quizzes WHERE quiz_code = ?";
    $result = $db->query($sql, [$quizCode]);

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
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
}
?> 