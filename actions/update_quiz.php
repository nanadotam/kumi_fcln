<?php
session_start();
require_once '../functions/auth_functions.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance();
    
    $db->begin_transaction();
    
    // Update quiz details
    $stmt = $db->query("
        UPDATE Quizzes 
        SET title = ?, description = ?, deadline = ?
        WHERE quiz_id = ? AND created_by = ?
    ", [
        $data['title'],
        $data['description'] ?? '',
        date('Y-m-d H:i:s', strtotime($data['due_date'] . ' ' . $data['due_time'])),
        $data['quiz_id'],
        $_SESSION['user_id']
    ]);
    
    // Handle questions
    foreach ($data['questions'] as $question) {
        if (isset($question['question_id'])) {
            // Update existing question
            $db->query("
                UPDATE Questions 
                SET question_text = ?, question_type = ?, points = ?
                WHERE question_id = ? AND quiz_id = ?
            ", [
                $question['text'],
                $question['type'],
                $question['points'],
                $question['question_id'],
                $data['quiz_id']
            ]);
            
            // Delete old answers
            $db->query("DELETE FROM Answers WHERE question_id = ?", [$question['question_id']]);
        } else {
            // Insert new question
            $db->query("
                INSERT INTO Questions (quiz_id, question_text, question_type, points)
                VALUES (?, ?, ?, ?)
            ", [
                $data['quiz_id'],
                $question['text'],
                $question['type'],
                $question['points']
            ]);
            $question['question_id'] = $db->lastInsertId();
        }
        
        // Insert/Update answers
        foreach ($question['options'] as $option) {
            $db->query("
                INSERT INTO Answers (question_id, answer_text, is_correct)
                VALUES (?, ?, ?)
            ", [
                $question['question_id'],
                $option['text'],
                $option['is_correct']
            ]);
        }
    }
    
    $db->commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 