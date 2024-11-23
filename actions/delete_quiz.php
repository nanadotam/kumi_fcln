<?php
session_start();
require_once '../functions/db_connect.php';

header('Content-Type: application/json');

try {
    // Get the JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    $quizId = $data['quiz_id'] ?? null;

    if (!$quizId) {
        throw new Exception('Quiz ID is required');
    }

    $db = Database::getInstance();
    
    // Delete the quiz
    $sql = "DELETE FROM Quizzes WHERE quiz_id = ?";
    $result = $db->query($sql, [$quizId]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to delete quiz');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 