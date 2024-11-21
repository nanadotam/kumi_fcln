<?php
require_once '../utils/Database.php';

function createQuiz($title, $description, $createdBy, $mode, $deadline = null) {
    $db = Database::getInstance();
    
    $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline) 
            VALUES (?, ?, ?, ?, ?)";
            
    return $db->query($sql, [$title, $description, $createdBy, $mode, $deadline]);
}

function getQuizzesByTeacher($teacherId) {
    $db = Database::getInstance();
    
    $sql = "SELECT q.*, COUNT(qr.result_id) as attempt_count 
            FROM Quizzes q 
            LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id 
            WHERE q.created_by = ? 
            GROUP BY q.quiz_id";
            
    $result = $db->query($sql, [$teacherId]);
    
    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = [
            'quiz_id' => (int)$row['quiz_id'],
            'title' => htmlspecialchars($row['title']),
            'description' => htmlspecialchars($row['description']),
            'mode' => $row['mode'],
            'attempt_count' => (int)$row['attempt_count'],
            'created_at' => $row['created_at']
        ];
    }
    
    return $quizzes;
}

function getQuizById($quizId) {
    $db = Database::getInstance();
    
    $sql = "SELECT q.*, u.first_name, u.last_name 
            FROM Quizzes q 
            JOIN Users u ON q.created_by = u.user_id 
            WHERE q.quiz_id = ?";
            
    $result = $db->query($sql, [$quizId]);
    
    if ($row = $result->fetch_assoc()) {
        return [
            'quiz_id' => (int)$row['quiz_id'],
            'title' => htmlspecialchars($row['title']),
            'description' => htmlspecialchars($row['description']),
            'creator_name' => htmlspecialchars($row['first_name'] . ' ' . $row['last_name']),
            'mode' => $row['mode'],
            'deadline' => $row['deadline'],
            'questions' => getQuizQuestions($quizId)
        ];
    }
    
    return null;
}

function getQuizQuestions($quizId) {
    $db = Database::getInstance();
    
    $sql = "SELECT q.*, GROUP_CONCAT(a.answer_id, ':::', a.answer_text, ':::', a.is_correct SEPARATOR '|||') as answers 
            FROM Questions q 
            LEFT JOIN Answers a ON q.question_id = a.question_id 
            WHERE q.quiz_id = ? 
            GROUP BY q.question_id";
            
    $result = $db->query($sql, [$quizId]);
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $answers = [];
        if ($row['answers']) {
            foreach (explode('|||', $row['answers']) as $answer) {
                list($id, $text, $isCorrect) = explode(':::', $answer);
                $answers[] = [
                    'answer_id' => (int)$id,
                    'text' => htmlspecialchars($text),
                    'is_correct' => (bool)$isCorrect
                ];
            }
        }
        
        $questions[] = [
            'question_id' => (int)$row['question_id'],
            'text' => htmlspecialchars($row['question_text']),
            'type' => $row['type'],
            'points' => (float)$row['points'],
            'answers' => $answers
        ];
    }
    
    return $questions;
}

function getAvailableQuizzes($studentId) {
    $db = Database::getInstance();
    
    $sql = "SELECT q.* 
            FROM Quizzes q 
            LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id AND qr.user_id = ?
            WHERE qr.result_id IS NULL 
            AND (q.deadline IS NULL OR q.deadline > NOW())
            ORDER BY q.created_at DESC";
            
    $result = $db->query($sql, [$studentId]);
    
    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = [
            'quiz_id' => (int)$row['quiz_id'],
            'title' => htmlspecialchars($row['title']),
            'description' => htmlspecialchars($row['description']),
            'mode' => $row['mode'],
            'deadline' => $row['deadline']
        ];
    }
    
    return $quizzes;
}

function getCompletedQuizzes($studentId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT q.title, qr.* 
                FROM QuizResults qr 
                JOIN Quizzes q ON qr.quiz_id = q.quiz_id 
                WHERE qr.user_id = ? 
                ORDER BY qr.submitted_at DESC";
                
        $result = $db->query($sql, [$studentId]);
        
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = [
                'result_id' => (int)$row['result_id'],
                'quiz_id' => (int)$row['quiz_id'],
                'title' => htmlspecialchars($row['title']),
                'score' => (float)$row['score'],
                'submitted_at' => $row['submitted_at']
            ];
        }
        
        return $quizzes;
    } catch (Exception $e) {
        error_log("Database error in getCompletedQuizzes: " . $e->getMessage());
        return [];
    }
}
