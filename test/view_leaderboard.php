<?php
require_once '../functions/quiz_functions.php';

// Get all quiz codes for testing
function getTestQuizzes() {
    try {
        $db = Database::getInstance();
        $sql = "SELECT quiz_id, title, quiz_code FROM Quizzes ORDER BY created_at DESC LIMIT 10";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$quizzes = getTestQuizzes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Leaderboards</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/leaderboard.css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .quiz-list {
            margin: 2rem 0;
        }
        .quiz-item {
            padding: 1rem;
            border: 1px solid #ddd;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Leaderboard Test Page</h1>
        
        <div class="actions">
            <a href="create_test_quiz.php" class="btn">Create Test Quiz</a>
        </div>

        <div class="quiz-list">
            <h2>Available Test Quizzes</h2>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="quiz-item">
                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                    <p>Quiz Code: <?= htmlspecialchars($quiz['quiz_code']) ?></p>
                    <a href="../view/live_leaderboard.php?code=<?= htmlspecialchars($quiz['quiz_code']) ?>" 
                       class="btn">View Leaderboard</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 