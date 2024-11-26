<?php
header('Content-Type: application/json');
session_start();
require_once '../utils/Database.php';

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $e->getMessage()]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Please log in first"]);
    exit;
}

// Insert quiz data
$title = $_POST['title'];
$description = $_POST['description'];
$created_by = $_SESSION['user_id'];
$mode = $_POST['mode'];
$deadline = $_POST['deadline'];
$shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
$shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
$max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
$time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

try {
    // Begin transaction
    $db->begin_transaction();

    // Insert quiz using query method
    $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline, 
            shuffle_questions, shuffle_answers, max_attempts, time_limit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $result = $db->query($sql, [
        $title, $description, $created_by, $mode, $deadline,
        $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit
    ]);
    
    $quiz_id = $db->lastInsertId();
    
    // Generate unique quiz code
    do {
        $quiz_code = generateCode(6);
        $check_result = $db->query("SELECT quiz_id FROM Quizzes WHERE quiz_code = ?", [$quiz_code]);
        $is_unique = ($check_result->num_rows === 0);
    } while (!$is_unique);

    // Update quiz with code
    $db->query("UPDATE Quizzes SET quiz_code = ? WHERE quiz_id = ?", [$quiz_code, $quiz_id]);
    
    // Commit transaction
    $db->commit();

    echo json_encode([
        'success' => true,
        'quiz_code' => $quiz_code,
        'message' => 'Quiz created successfully'
    ]);
    
} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 