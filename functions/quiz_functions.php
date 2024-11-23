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
    
    $sql = "SELECT q.quiz_id, q.title, q.description, q.mode, q.deadline, q.created_at 
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
            'deadline' => $row['deadline'],
            'created_at' => $row['created_at']
        ];
    }
    
    return $quizzes;
}

function getCompletedQuizzes($userId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT 
                qr.result_id,
                qr.quiz_id,
                qr.score,
                qr.submitted_at,
                q.title,
                q.description,
                q.mode,
                (SELECT COUNT(*) 
                 FROM Responses r 
                 WHERE r.result_id = qr.result_id AND r.is_correct = 1) as correct_answers,
                (SELECT COUNT(*) 
                 FROM Questions qs 
                 WHERE qs.quiz_id = q.quiz_id) as total_questions
                FROM QuizResults qr
                JOIN Quizzes q ON qr.quiz_id = q.quiz_id
                WHERE qr.user_id = ?
                ORDER BY qr.submitted_at DESC";
        
        $result = $db->query($sql, [$userId]);
        
        $completedQuizzes = [];
        while ($row = $result->fetch_assoc()) {
            $completedQuizzes[] = [
                'result_id' => (int)$row['result_id'],
                'quiz_id' => (int)$row['quiz_id'],
                'title' => htmlspecialchars($row['title']),
                'description' => htmlspecialchars($row['description']),
                'score' => (float)$row['score'],
                'submitted_at' => $row['submitted_at'],
                'mode' => $row['mode'],
                'correct_answers' => (int)$row['correct_answers'],
                'total_questions' => (int)$row['total_questions'],
                'performance' => $row['score'] >= 70 ? 'pass' : 'fail'
            ];
        }
        
        return $completedQuizzes;
    } catch (Exception $e) {
        error_log("Error getting completed quizzes: " . $e->getMessage());
        return [];
    }
}

function getCorrectAnswer($questionId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT answer_text 
                FROM Answers 
                WHERE question_id = ? AND is_correct = 1";
        $result = $db->query($sql, [$questionId]);
        $answer = $result->fetch_assoc();
        return $answer ? $answer['answer_text'] : 'Not available';
    } catch (Exception $e) {
        error_log("Error getting correct answer: " . $e->getMessage());
        return 'Not available';
    }
}

function validateMultipleChoice($questionId, $answerId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT is_correct 
                FROM Answers 
                WHERE question_id = ? 
                AND answer_id = ?";
                
        $result = $db->query($sql, [$questionId, $answerId]);
        
        if ($row = $result->fetch_assoc()) {
            return (bool)$row['is_correct'];
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Error validating multiple choice answer: " . $e->getMessage());
        return false;
    }
}

function validateTextAnswer($questionId, $response) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT answer_text, model_answer 
                FROM Answers 
                WHERE question_id = ? AND is_correct = 1";
                
        $result = $db->query($sql, [$questionId]);
        $row = $result->fetch_assoc();
        
        if (!$row) return 0;
        
        // Simple string comparison
        $similarity = similar_text(
            strtolower(trim($response)), 
            strtolower(trim($row['answer_text'])), 
            $percent
        );
        
        return $percent >= 80 ? 1 : 0;
        
    } catch (Exception $e) {
        error_log("Error validating text answer: " . $e->getMessage());
        return 0;
    }
}

function saveQuizResults($userId, $quizId, $score, $responses) {
    try {
        $db = Database::getInstance();
        
        // Start transaction
        $db->begin_transaction();
        
        // Insert quiz result
        $sql = "INSERT INTO QuizResults (user_id, quiz_id, score, submitted_at) 
                VALUES (?, ?, ?, NOW())";
        $db->query($sql, [$userId, $quizId, $score]);
        
        $resultId = $db->insert_id();
        
        // Insert individual responses
        foreach ($responses as $response) {
            if ($response['type'] === 'short_answer') {
                // For text/short answers
                $sql = "INSERT INTO Responses (result_id, question_id, text_response, is_correct) 
                        VALUES (?, ?, ?, ?)";
                $db->query($sql, [
                    $resultId,
                    $response['question_id'],
                    $response['response'],
                    $response['is_correct'] ? 1 : 0
                ]);
            } else {
                // For multiple choice answers - verify answer_id exists first
                $verifySQL = "SELECT 1 FROM Answers WHERE answer_id = ?";
                $result = $db->query($verifySQL, [$response['response']]);
                
                if ($result->num_rows > 0) {
                    $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct) 
                            VALUES (?, ?, ?, ?)";
                    $db->query($sql, [
                        $resultId,
                        $response['question_id'],
                        $response['response'],
                        $response['is_correct'] ? 1 : 0
                    ]);
                } else {
                    // If answer doesn't exist, store without selected_answer_id
                    $sql = "INSERT INTO Responses (result_id, question_id, is_correct) 
                            VALUES (?, ?, ?)";
                    $db->query($sql, [
                        $resultId,
                        $response['question_id'],
                        $response['is_correct'] ? 1 : 0
                    ]);
                }
            }
        }
        
        $db->commit();
        return $resultId;
        
    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollback();
        }
        error_log("Error saving quiz results: " . $e->getMessage());
        throw $e;
    }
}

function canAccessQuiz($userId, $quizId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT 1 FROM Quizzes q
                LEFT JOIN QuizResults qr ON q.quiz_id = qr.quiz_id AND qr.user_id = ?
                WHERE q.quiz_id = ? 
                AND (q.deadline IS NULL OR q.deadline > NOW())
                AND qr.result_id IS NULL";
                
        $result = $db->query($sql, [$userId, $quizId]);
        return $result->num_rows > 0;
        
    } catch (Exception $e) {
        error_log("Error checking quiz access: " . $e->getMessage());
        return false;
    }
}

function saveQuizWithAnswers($quizData) {
    try {
        $db = Database::getConnection();
        $conn = $db->getConnection();
        
        $conn->begin_transaction();
        
        // Insert quiz
        $sql = "INSERT INTO Quizzes (title, description, created_by, deadline) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $quizData['title'], $quizData['description'], 
                         $quizData['created_by'], $quizData['deadline']);
        $stmt->execute();
        $quizId = $conn->insert_id;
        
        // Insert questions and answers
        foreach ($quizData['questions'] as $question) {
            $sql = "INSERT INTO Questions (quiz_id, question_text, question_type, points) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issi", $quizId, $question['text'], 
                            $question['type'], $question['points']);
            $stmt->execute();
            $questionId = $conn->insert_id;
            
            // Insert options and mark correct answers
            if (isset($question['options'])) {
                foreach ($question['options'] as $option) {
                    $sql = "INSERT INTO Options (question_id, option_text, is_correct) 
                            VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $isCorrect = $option['is_correct'] ? 1 : 0;
                    $stmt->bind_param("isi", $questionId, $option['text'], $isCorrect);
                    $stmt->execute();
                }
            }
        }
        
        $conn->commit();
        return $quizId;
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        throw $e;
    }
}
