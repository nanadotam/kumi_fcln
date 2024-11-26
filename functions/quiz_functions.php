<?php
require_once __DIR__ . '/../utils/Database.php';

function createQuiz($title, $description, $createdBy, $mode, $deadline = null) {
    $db = Database::getInstance();
    
    $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline) 
            VALUES (?, ?, ?, ?, ?)";
            
    return $db->query($sql, [$title, $description, $createdBy, $mode, $deadline]);
}

function getQuizzesByTeacher($teacherId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT * FROM Quizzes WHERE created_by = ? ORDER BY created_at DESC";
        $result = $db->query($sql, [$teacherId]);
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting quizzes: " . $e->getMessage());
        return [];
    }
}

function getQuizById($quizId) {
    $db = new mysqli("localhost", "root", "", "kumidb");
    
    if ($db->connect_error) {
        return null;
    }

    // Get quiz details
    $stmt = $db->prepare("
        SELECT q.*, u.first_name, u.last_name 
        FROM Quizzes q
        JOIN Users u ON q.created_by = u.user_id
        WHERE q.quiz_id = ?
    ");
    
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $quiz = $result->fetch_assoc();
    
    // Get questions and answers
    $stmt = $db->prepare("
        SELECT q.*, a.answer_id, a.answer_text, a.is_correct 
        FROM Questions q
        LEFT JOIN Answers a ON q.question_id = a.question_id
        WHERE q.quiz_id = ?
        ORDER BY q.order_position, q.question_id
    ");
    
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['question_id'];
        if (!isset($questions[$questionId])) {
            $questions[$questionId] = [
                'question_id' => $questionId,
                'text' => $row['question_text'],
                'type' => $row['type'],
                'points' => $row['points'],
                'answers' => []
            ];
        }
        if ($row['answer_id']) {
            $questions[$questionId]['answers'][] = [
                'answer_id' => $row['answer_id'],
                'text' => $row['answer_text'],
                'is_correct' => $row['is_correct']
            ];
        }
    }
    
    $quiz['questions'] = array_values($questions);
    
    $db->close();
    return $quiz;
}

function getQuizQuestions($quizId) {
    try {
        $db = Database::getInstance();
        
        // Get questions
        $sql = "SELECT * FROM Questions 
                WHERE quiz_id = ? 
                ORDER BY order_position";
                
        $result = $db->query($sql, [$quizId]);
        $questions = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get answers for each question
        foreach ($questions as &$question) {
            $sql = "SELECT * FROM Answers 
                    WHERE question_id = ?
                    ORDER BY answer_id";
            $result = $db->query($sql, [$question['question_id']]);
            $question['answers'] = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return $questions;
        
    } catch (Exception $e) {
        error_log("Error getting quiz questions: " . $e->getMessage());
        return [];
    }
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
        $db->begin_transaction();
        
        // Insert quiz result
        $sql = "INSERT INTO QuizResults (user_id, quiz_id, score, submitted_at) 
                VALUES (?, ?, ?, NOW())";
        $db->query($sql, [$userId, $quizId, $score]);
        
        $resultId = $db->lastInsertId();
        
        // Insert responses
        foreach ($responses as $response) {
            if ($response['type'] === 'short_answer') {
                $sql = "INSERT INTO Responses (result_id, question_id, text_response, is_correct) 
                        VALUES (?, ?, ?, ?)";
                $db->query($sql, [
                    $resultId,
                    $response['question_id'],
                    $response['response'],
                    $response['is_correct'] ? 1 : 0
                ]);
            } else {
                $sql = "INSERT INTO Responses (result_id, question_id, selected_answer_id, is_correct) 
                        VALUES (?, ?, ?, ?)";
                $db->query($sql, [
                    $resultId,
                    $response['question_id'],
                    $response['selected_answer_id'],
                    $response['is_correct'] ? 1 : 0
                ]);
            }
        }
        
        $db->commit();
        return $resultId;
    } catch (Exception $e) {
        $db->rollback();
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

function verifyQuizOwnership($quizId, $teacherId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT created_by FROM Quizzes WHERE quiz_id = ?";
        $result = $db->query($sql, [$quizId]);
        $quiz = $result->fetch_assoc();
        return $quiz && $quiz['created_by'] == $teacherId;
    } catch (Exception $e) {
        error_log("Error verifying quiz ownership: " . $e->getMessage());
        return false;
    }
}

function deleteQuiz($quizId) {
    try {
        $db = Database::getInstance();
        $db->begin_transaction();
        
        // Delete related records using foreign key cascading
        $sql = "DELETE FROM Quizzes WHERE quiz_id = ?";
        $db->query($sql, [$quizId]);
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        error_log("Error deleting quiz: " . $e->getMessage());
        return false;
    }
}
