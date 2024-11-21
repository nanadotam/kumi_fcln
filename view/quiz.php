<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$quizId = $_GET['id'] ?? null;
$quiz = null;
if ($quizId) {
    $quiz = getQuizById($quizId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $quizId ? 'Edit Quiz' : 'Create Quiz' ?> - Kumi</title>
    <link rel="stylesheet" href="../assets/css/quiz.css">
</head>
<body>
    <div class="menu-bar">
        <button id="preview-btn">👀 Preview</button>
        <button id="view-responses-btn">📊 View Responses</button>
        <a href="teacher_dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
    <div class="container">
        <h1><?= $quizId ? 'Edit Quiz' : 'Create a Quiz' ?></h1>
        <h2>Unleash Your Inner Quizmaster! 🧠✨</h2>
        
        <form id="quiz-form" data-quiz-id="<?= $quizId ?? '' ?>">
            <div class="quiz-settings">
                <input type="text" id="quiz-title" placeholder="Quiz Title" 
                       value="<?= $quiz['title'] ?? '' ?>" required>
                <textarea id="quiz-description" placeholder="Quiz Description"><?= $quiz['description'] ?? '' ?></textarea>
                <select id="quiz-mode" required>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                </select>
                <input type="datetime-local" id="quiz-deadline">
            </div>
            
            <div id="quiz-container">
                <?php if ($quiz): ?>
                    <?php foreach ($quiz['questions'] as $question): ?>
                        <!-- Render existing questions -->
                        <div class="question-container" data-question-id="<?= $question['question_id'] ?>">
                            <!-- Question content here -->
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button type="button" id="add-question-btn">Add Question</button>
            <button type="submit" id="save-quiz-btn">Save Quiz</button>
        </form>
    </div>

    <script src="../assets/js/quiz.js"></script>
</body>
</html>
