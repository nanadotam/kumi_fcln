<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

// Ensure only teachers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Get quiz ID from URL
$quizId = $_GET['id'] ?? null;
$quiz = null;
$questions = [];

if ($quizId) {
    try {
        $db = Database::getInstance();
        
        // Get quiz details
        $sql = "SELECT * FROM Quizzes WHERE quiz_id = ? AND created_by = ?";
        $result = $db->query($sql, [$quizId, $_SESSION['user_id']]);
        $quiz = $result->fetch_assoc();
        
        if (!$quiz) {
            throw new Exception("Quiz not found or access denied");
        }
        
        // Get questions with their options
        $sql = "SELECT 
                    q.question_id,
                    q.question_text,
                    q.question_type,
                    q.points,
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            'id', a.answer_id,
                            'text', a.answer_text,
                            'is_correct', a.is_correct
                        )
                    ) as options
                FROM Questions q
                LEFT JOIN Answers a ON q.question_id = a.question_id
                WHERE q.quiz_id = ?
                GROUP BY q.question_id
                ORDER BY q.question_id";
        
        $result = $db->query($sql, [$quizId]);
        $questions = $result->fetch_all(MYSQLI_ASSOC);
        
        // Format the options for each question
        foreach ($questions as &$question) {
            if ($question['options']) {
                $question['options'] = explode(',', $question['options']);
            } else {
                $question['options'] = [];
            }
        }
        unset($question);
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: quiz.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/create_quiz.css">
    <link rel="stylesheet" href="../assets/css/edit_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <div class="quiz-creator">
        <span class="edit-mode-badge">Edit Mode</span>
        
        <div class="quiz-header">
            <input type="text" id="quizTitle" class="quiz-title" 
                   value="<?= htmlspecialchars($quiz['title']) ?>" 
                   placeholder="Untitled Quiz">
            <input type="text" id="quizDescription" class="quiz-description" 
                   value="<?= htmlspecialchars($quiz['description']) ?>" 
                   placeholder="Quiz description">
            
            <div class="quiz-settings">
                <div class="date-time-picker">
                    <div class="setting-group">
                        <label>Due Date:</label>
                        <input type="date" id="quizDueDate" class="date-picker" 
                               value="<?= date('Y-m-d', strtotime($quiz['deadline'])) ?>">
                    </div>
                    <div class="setting-group">
                        <label>Due Time:</label>
                        <input type="time" id="quizDueTime" class="time-picker" 
                               value="<?= date('H:i', strtotime($quiz['deadline'])) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div id="questionsContainer" class="questions-container">
            <!-- Questions will be loaded here -->
        </div>

        <div class="action-buttons">
            <button id="addQuestionBtn" class="btn-add">
                <i class='bx bx-plus'></i> Add Question
            </button>
            <button id="saveQuizBtn" class="btn-save">
                <i class='bx bx-save'></i> Save Changes
            </button>
        </div>
    </div>

    <script>
        // Initialize quiz data
        const quizId = <?= json_encode($quizId) ?>;
        const existingQuestions = <?= json_encode($questions) ?>;
        let questionCount = existingQuestions.length;
        
        // Load existing questions on page load
        document.addEventListener('DOMContentLoaded', () => {
            existingQuestions.forEach(question => {
                createQuestion(question);
            });
        });
    </script>
    <script src="../assets/js/quiz.js"></script>
</body>
</html>
