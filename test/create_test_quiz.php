<?php
require_once '../functions/quiz_functions.php';

// Create a test quiz with a code
function createTestQuiz() {
    try {
        $db = Database::getInstance();
        
        // Generate a random quiz code
        $quizCode = substr(md5(uniqid()), 0, 8);
        
        // Insert test quiz
        $sql = "INSERT INTO Quizzes (title, description, created_by, mode, quiz_code) 
                VALUES (?, ?, ?, ?, ?)";
        
        $db->query($sql, [
            'Test Quiz',
            'This is a test quiz for leaderboard',
            1, // Assuming user_id 1 exists
            'standard',
            $quizCode
        ]);
        
        return $quizCode;
        
    } catch (Exception $e) {
        echo "Error creating test quiz: " . $e->getMessage();
        return null;
    }
}

// Insert some test scores
function insertTestScores($quizCode) {
    try {
        $db = Database::getInstance();
        
        // Get quiz_id from code
        $sql = "SELECT quiz_id FROM Quizzes WHERE quiz_code = ?";
        $result = $db->query($sql, [$quizCode]);
        $quiz = $result->fetch_assoc();
        
        if (!$quiz) {
            throw new Exception("Quiz not found");
        }
        
        // Sample test scores
        $testScores = [
            ['user_id' => 1, 'score' => 95],
            ['user_id' => 2, 'score' => 88],
            ['user_id' => 3, 'score' => 92],
            ['user_id' => 4, 'score' => 78],
            ['user_id' => 5, 'score' => 85]
        ];
        
        foreach ($testScores as $score) {
            $sql = "INSERT INTO QuizLeaderboard (quiz_id, user_id, score, completion_time) 
                    VALUES (?, ?, ?, NOW())";
            $db->query($sql, [
                $quiz['quiz_id'],
                $score['user_id'],
                $score['score']
            ]);
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "Error inserting test scores: " . $e->getMessage();
        return false;
    }
}

// Run the test
$quizCode = createTestQuiz();
if ($quizCode) {
    echo "Test quiz created with code: " . $quizCode . "<br>";
    if (insertTestScores($quizCode)) {
        echo "Test scores inserted successfully<br>";
        echo "View leaderboard at: <a href='../view/live_leaderboard.php?code=" . $quizCode . "'>View Leaderboard</a>";
    }
} 