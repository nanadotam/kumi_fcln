<?php
session_start();
require_once '../utils/Database.php';
header('Content-Type: application/json');

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);
$quizId = $data['quiz_id'] ?? null;

if (!$quizId) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz ID is required'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Start transaction
    $db->begin_transaction();
    
    // First, verify the quiz exists and belongs to the teacher
    $sql = "SELECT quiz_id FROM Quizzes WHERE quiz_id = ? AND created_by = ?";
    $result = $db->query($sql, [$quizId, $_SESSION['user_id']]);
    
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Quiz not found or you do not have permission to delete it');
    }

    // Delete related records first (if not handled by foreign key constraints)
    $sql = "DELETE FROM Questions WHERE quiz_id = ?";
    $db->query($sql, [$quizId]);
    
    // Then delete the quiz
    $sql = "DELETE FROM Quizzes WHERE quiz_id = ? AND created_by = ?";
    $result = $db->query($sql, [$quizId, $_SESSION['user_id']]);

    $db->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Quiz deleted successfully'
    ]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 