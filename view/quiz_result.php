<?php
session_start();
require_once '../utils/Database.php';
require_once '../functions/quiz_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$resultId = $_GET['id'] ?? null;
if (!$resultId) {
    header('Location: student_dashboard.php');
    exit();
}

try {
    $db = Database::getInstance();
    
    // Get quiz result details with quiz info
    $sql = "SELECT qr.*, q.title, q.description 
            FROM QuizResults qr
            JOIN Quizzes q ON qr.quiz_id = q.quiz_id
            WHERE qr.result_id = ? AND (qr.user_id = ? OR q.created_by = ?)";
    
    $result = $db->query($sql, [$resultId, $_SESSION['user_id'], $_SESSION['user_id']]);
    $quizResult = $result->fetch_assoc();
    
    if (!$quizResult) {
        throw new Exception("Quiz result not found or access denied");
    }
    
    // Get responses with question and answer details
    $sql = "SELECT r.*, q.question_text, q.points, q.type,
            CASE 
                WHEN q.type = 'short_answer' THEN r.text_response
                ELSE a.answer_text 
            END as user_answer,
            CASE 
                WHEN q.type = 'short_answer' THEN 
                    (SELECT answer_text FROM Answers WHERE question_id = q.question_id AND is_correct = 1)
                ELSE 
                    (SELECT answer_text FROM Answers WHERE answer_id = r.selected_answer_id)
            END as answer_text
            FROM Responses r
            JOIN Questions q ON r.question_id = q.question_id
            LEFT JOIN Answers a ON r.selected_answer_id = a.answer_id
            WHERE r.result_id = ?
            ORDER BY q.question_id";
            
    $responses = $db->query($sql, [$resultId])->fetch_all(MYSQLI_ASSOC);
    
    $quizCodeResult = $db->query("SELECT quiz_code FROM Quizzes WHERE quiz_id = ?", [$quizId]);
    $quizCode = $quizCodeResult->fetch_assoc()['quiz_code'];
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: student_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - <?= htmlspecialchars($quizResult['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/quiz_result.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <main class="result-page">
        <div class="result-header">
            <h1><?= htmlspecialchars($quizResult['title']) ?></h1>
            <div class="score-display">
                <span class="score <?= $quizResult['score'] >= 70 ? 'passing' : 'failing' ?>">
                    <?= number_format($quizResult['score'], 1) ?>%
                </span>
                <span class="submission-date">
                    Submitted on <?= date('M d, Y \a\t h:i A', strtotime($quizResult['submitted_at'])) ?>
                </span>
            </div>
        </div>

        <div class="responses-container">
            <?php foreach ($responses as $index => $response): ?>
                <div class="response-card <?= $response['is_correct'] ? 'correct' : 'incorrect' ?>">
                    <div class="question-header">
                        <h3>Question <?= $index + 1 ?></h3>
                        <span class="points"><?= $response['points'] ?> points</span>
                    </div>
                    
                    <p class="question-text"><?= htmlspecialchars($response['question_text']) ?></p>
                    
                    <div class="answer-section">
                        <p class="your-answer">
                            Your answer: <?= htmlspecialchars($response['user_answer'] ?? 'No answer provided') ?>
                        </p>
                        
                        <?php if (!$response['is_correct']): ?>
                            <p class="correct-answer">
                                Correct answer: <?= htmlspecialchars($response['answer_text']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="result-indicator">
                        <?php if ($response['is_correct']): ?>
                            <i class='bx bx-check-circle'></i> Correct
                        <?php else: ?>
                            <i class='bx bx-x-circle'></i> Incorrect
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($response['type'] === 'text'): ?>
                        <div class="text-response">
                            <h4>Student's Answer:</h4>
                            <div class="response-text">
                                <?= nl2br(htmlspecialchars($response['text_response'])) ?>
                            </div>
                            <?php if ($_SESSION['role'] === 'teacher'): ?>
                                <div class="grading-section">
                                    <label for="points_<?= $response['question_id'] ?>">Points:</label>
                                    <input type="number" 
                                           id="points_<?= $response['question_id'] ?>"
                                           class="points-input"
                                           min="0"
                                           max="<?= $response['max_points'] ?>"
                                           value="<?= $response['awarded_points'] ?? 0 ?>"
                                           onchange="updatePoints(<?= $response['question_id'] ?>, this.value)">
                                    <span class="max-points">/ <?= $response['max_points'] ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="actions">
            <a href="student_dashboard.php" class="btn">Back to Dashboard</a>
            <a href="quiz.php" class="btn">Take Another Quiz</a>
            <a href="interactive_leaderboard.php?quiz_id=<?= $quizId ?>" class="btn btn-primary">
                <i class='bx bx-trophy'></i> View Leaderboard
            </a>
        </div>
    </main>
</body>
</html> 