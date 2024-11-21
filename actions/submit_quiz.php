<?php
session_start();
require_once '../utils/Database.php';
require_once '../functions/quiz_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    $quizId = $_POST['quiz_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    $groupId = $_POST['group_id'] ?? null;
    $responses = $_POST['responses'] ?? [];
    
    if (!$quizId || !$userId || empty($responses)) {
        throw new Exception('Missing required data');
    }
    
    $db = new Database();
    $db->getConnection()->begin_transaction();
    
    // Create quiz result
    $sql = "INSERT INTO QuizResults (quiz_id, user_id, group_id, score) VALUES (?, ?, ?, 0)";
    $result = $db->query($sql, [$quizId, $userId, $groupId]);
    $resultId = $db->getConnection()->insert_id;
    
    $totalScore = 0;
    
    // Process each response
    foreach ($responses as $questionId => $answerId) {
        $isCorrect = checkAnswer($db, $questionId, $answerId);
        $points = getQuestionPoints($db, $questionId);
        
        if ($isCorrect) {
            $totalScore += $points;
        }
        
        $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct) 
                VALUES (?, ?, ?, ?)";
        $db->query($sql, [$resultId, $questionId, $answerId, $isCorrect]);
    }
    
    // Update final score
    $sql = "UPDATE QuizResults SET score = ? WHERE result_id = ?";
    $db->query($sql, [$totalScore, $resultId]);
    
    $db->getConnection()->commit();
    
    echo json_encode([
        'success' => true,
        'score' => $totalScore,
        'message' => 'Quiz submitted successfully!'
    ]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->getConnection()->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 