<?php
session_start();
require_once '../functions/quiz_functions.php';

$quizId = $_GET['id'] ?? null;
if (!$quizId) {
    header('Location: dashboard.php');
    exit();
}

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
    <title>Take Quiz - <?= $quiz['title'] ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="quiz-container">
        <h1><?= $quiz['title'] ?></h1>
        <p><?= $quiz['description'] ?></p>
        
        <form id="quiz-form" data-quiz-id="<?= $quizId ?>">
            <?php foreach ($quiz['questions'] as $index => $question): ?>
                <div class="question-card">
                    <h3>Question <?= $index + 1 ?></h3>
                    <p><?= $question['text'] ?></p>
                    
                    <?php if ($question['type'] === 'multiple_choice'): ?>
                        <?php foreach ($question['answers'] as $answer): ?>
                            <div class="answer-option">
                                <input type="radio" 
                                       name="q_<?= $question['question_id'] ?>" 
                                       value="<?= $answer['answer_id'] ?>"
                                       required>
                                <label><?= $answer['text'] ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="submit-btn">Submit Quiz</button>
        </form>
    </div>
    
    <script src="../assets/js/take_quiz.js"></script>
</body>
</html> 