<?php
session_start();
require_once '../utils/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

try {
    $db = Database::getInstance();
    
    $quiz_id = $_POST['quiz_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $mode = $_POST['mode'];
    $deadline = $_POST['deadline'];
    $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
    $shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
    $max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
    $time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

    // Start transaction
    $db->begin_transaction();

    // Update quiz details
    $sql = "UPDATE Quizzes 
            SET title = ?, 
                description = ?, 
                mode = ?, 
                deadline = ?, 
                shuffle_questions = ?, 
                shuffle_answers = ?, 
                max_attempts = ?, 
                time_limit = ? 
            WHERE quiz_id = ?";
    
    $db->query($sql, [
        $title, 
        $description, 
        $mode, 
        $deadline,
        $shuffle_questions, 
        $shuffle_answers, 
        $max_attempts, 
        $time_limit, 
        $quiz_id
    ]);

    $db->commit();
    
    echo json_encode(['success' => true, 'message' => 'Quiz updated successfully']);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log("Error updating quiz: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating quiz']);
} 