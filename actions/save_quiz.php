<?php
session_start();
require_once '../utils/Database.php';
require_once '../functions/auth_functions.php';

// Ensure only teachers can access this endpoint
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance();
    
    $db->begin_transaction();
    
    // Validate required fields
    if (empty($data['title']) || empty($data['questions'])) {
        throw new Exception('Missing required fields');
    }
    
    // Format due date and time
    $dueDateTime = null;
    if (!empty($data['due_date']) && !empty($data['due_time'])) {
        $dueDateTime = date('Y-m-d H:i:s', strtotime($data['due_date'] . ' ' . $data['due_time']));
    }
    
    // Save quiz details
    $stmt = $db->query("
        INSERT INTO Quizzes (title, description, deadline, created_by)
        VALUES (?, ?, ?, ?)
    ", [
        $data['title'],
        $data['description'] ?? '',
        $dueDateTime,
        $_SESSION['user_id']
    ]);
    
    $quizId = $db->lastInsertId();
    
    // Save questions
    foreach ($data['questions'] as $question) {
        // Insert question
        $stmt = $db->query("
            INSERT INTO Questions (quiz_id, question_text, question_type, points)
            VALUES (?, ?, ?, ?)
        ", [
            $quizId,
            $question['text'],
            $question['type'],
            $question['points'] ?? 1
        ]);
        
        $questionId = $db->lastInsertId();
        
        // Handle different question types
        switch ($question['type']) {
            case 'true_false':
            case 'multiple_choice':
                foreach ($question['options'] as $option) {
                    $stmt = $db->query("
                        INSERT INTO Answers (question_id, answer_text, is_correct)
                        VALUES (?, ?, ?)
                    ", [
                        $questionId,
                        $option['text'],
                        $option['is_correct']
                    ]);
                }
                break;
                
            case 'short_answer':
                $stmt = $db->query("
                    INSERT INTO Answers (question_id, answer_text, is_correct, model_answer)
                    VALUES (?, ?, 1, ?)
                ", [
                    $questionId,
                    $question['model_answer'],
                    $question['model_answer']
                ]);
                break;
        }
    }
    
    $db->commit();
    echo json_encode(['success' => true, 'quiz_id' => $quizId]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>