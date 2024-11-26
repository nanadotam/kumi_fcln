<?php
session_start();
require_once '../functions/quiz_functions.php';
require_once '../functions/auth_functions.php';

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

// Get quiz using the same function as take_quiz.php
$quiz = getQuizById($quizId);
if (!$quiz) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <div class="preview-mode-banner">
        <i class='bx bx-show'></i> Preview Mode - This is how students will see your quiz
    </div>

    <div class="quiz-wrapper">
        <a href="view_quiz.php?id=<?= $quizId ?>" class="back-link">
            <i class='bx bx-arrow-back'></i> Back to Quiz Overview
        </a>

        <div class="quiz-container">
            <div class="quiz-header">
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
                <p class="description"><?= htmlspecialchars($quiz['description']) ?></p>
            </div>
            
            <form>
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <h3>Question <?= $index + 1 ?></h3>
                            <span class="question-type"><?= ucfirst(str_replace('_', ' ', $question['type'])) ?></span>
                        </div>
                        <p class="question-text"><?= htmlspecialchars($question['text']) ?></p>
                        
                        <div class="answer-container">
                            <?php if ($question['type'] === 'multiple_choice' || $question['type'] === 'true_false'): ?>
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               disabled
                                               id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                               name="q_<?= $question['question_id'] ?>">
                                        <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                            <?= htmlspecialchars($answer['text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif ($question['type'] === 'short_answer'): ?>
                                <div class="text-answer">
                                    <textarea 
                                        class="text-input"
                                        placeholder="Students will enter their answer here..."
                                        disabled
                                        rows="6"
                                    ></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="quiz-actions">
                    <button type="button" class="submit-btn" disabled>Submit Quiz</button>
                    <p class="preview-note">This is a preview - submissions are disabled</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 