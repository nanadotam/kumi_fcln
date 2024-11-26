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
    
    // Verify that the quiz belongs to the current teacher
    $sql = "SELECT created_by FROM Quizzes WHERE quiz_id = ?";
    $result = $db->query($sql, [$quizId]);
    $quiz = $result->fetch_assoc();

    if (!$quiz || $quiz['created_by'] !== $_SESSION['user_id']) {
        echo json_encode([
            'success' => false,
            'message' => 'You do not have permission to delete this quiz'
        ]);
        exit;
    }

    // Start transaction
    $db->begin_transaction();

    // The foreign key constraints will handle the deletion of related records
    $sql = "DELETE FROM Quizzes WHERE quiz_id = ? AND created_by = ?";
    $result = $db->query($sql, [$quizId, $_SESSION['user_id']]);

    if ($result) {
        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Quiz deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete quiz');
    }

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