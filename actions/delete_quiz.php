<?php
session_start();
require_once '../utils/Database.php';
require_once 'quiz_functions.php';

// Set JSON content type header
header('Content-Type: application/json');

// Ensure request is POST and user is logged in
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);
$quizId = $data['quiz_id'] ?? null;

if (!$quizId) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID is required']);
    exit();
}

try {
    $db = Database::getInstance();
    
    // Start transaction
    $db->begin_transaction();
    
    // Delete related records first
    $tables = ['QuizResults', 'Questions', 'Quizzes'];
    
    foreach ($tables as $table) {
        $sql = "DELETE FROM $table WHERE quiz_id = ?";
        $db->query($sql, [$quizId]);
    }
    
    // Commit transaction
    $db->commit();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log("Error deleting quiz: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} 