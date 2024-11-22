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
    <link rel="stylesheet" href="../assets/css/take_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="quiz-progress-floating">
        <div class="progress-inner">
            <div class="progress-text">
                Question <span id="currentQuestion">0</span> of <?= count($quiz['questions']) ?>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
    </div>

    <div class="quiz-wrapper">
        <div class="quiz-container">
            <div class="quiz-header">
                <h1><?= $quiz['title'] ?></h1>
                <p class="description"><?= $quiz['description'] ?></p>
            </div>
            
            <form id="quiz-form" data-quiz-id="<?= $quizId ?>">
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="question-card" data-question="<?= $index + 1 ?>">
                        <div class="question-header">
                            <h3>Question <?= $index + 1 ?></h3>
                            <span class="question-type"><?= ucfirst($question['type']) ?></span>
                        </div>
                        <p class="question-text"><?= $question['text'] ?></p>
                        
                        <div class="answer-container">
                            <?php if ($question['type'] === 'multiple_choice'): ?>
                                <?php foreach ($question['answers'] as $answer): ?>
                                    <div class="answer-option">
                                        <input type="radio" 
                                               id="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>"
                                               name="q_<?= $question['question_id'] ?>" 
                                               value="<?= $answer['answer_id'] ?>"
                                               required>
                                        <label for="q<?= $question['question_id'] ?>_a<?= $answer['answer_id'] ?>">
                                            <?= $answer['text'] ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif ($question['type'] === 'text'): ?>
                                <div class="text-answer">
                                    <textarea 
                                        name="q_<?= $question['question_id'] ?>"
                                        class="text-input"
                                        placeholder="Enter your answer here..."
                                        required
                                        rows="6"
                                    ></textarea>
                                    <div class="text-guidelines">
                                        <i class='bx bx-info-circle'></i>
                                        <span>Write your answer clearly and concisely.</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="quiz-actions">
                    <button type="submit" class="submit-btn">Submit Quiz</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/take_quiz.js"></script>
</body>
</html> 