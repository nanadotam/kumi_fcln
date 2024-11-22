<?php
session_start();
require_once '../utils/Database.php';
header('Content-Type: application/json');

try {
    // Validate user session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    // Get and decode JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate quiz_id
    if (!isset($data['quiz_id']) || !is_numeric($data['quiz_id'])) {
        throw new Exception('Invalid or missing quiz_id');
    }
    
    // Validate responses
    if (!isset($data['responses']) || !is_array($data['responses'])) {
        throw new Exception('Invalid or missing responses');
    }

    $userId = $_SESSION['user_id'];
    $quizId = (int)$data['quiz_id'];
    $responses = $data['responses'];

    // Verify quiz exists
    $db = Database::getInstance();
    $sql = "SELECT quiz_id FROM Quizzes WHERE quiz_id = ?";
    $result = $db->query($sql, [$quizId]);
    
    if (!$result->num_rows) {
        throw new Exception('Quiz not found');
    }

    // Start transaction
    $db->begin_transaction();

    // Create quiz result
    $sql = "INSERT INTO QuizResults (quiz_id, user_id, score, submitted_at) 
            VALUES (?, ?, 0, NOW())";
    $db->query($sql, [$quizId, $userId]);
    
    $resultId = $db->insert_id();

    // Process responses
    foreach ($responses as $questionId => $response) {
        // Verify question belongs to this quiz
        $sql = "SELECT type FROM Questions WHERE question_id = ? AND quiz_id = ?";
        $result = $db->query($sql, [$questionId, $quizId]);
        
        if (!$result->num_rows) {
            throw new Exception('Invalid question ID');
        }
        
        $question = $result->fetch_assoc();
        
        if ($question['type'] === 'short_answer') {
            $sql = "INSERT INTO Responses (result_id, question_id, text_response) 
                    VALUES (?, ?, ?)";
            $db->query($sql, [$resultId, $questionId, $response]);
        } else {
            $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id) 
                    VALUES (?, ?, ?)";
            $db->query($sql, [$resultId, $questionId, $response]);
        }
    }

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'result_id' => $resultId
    ]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 