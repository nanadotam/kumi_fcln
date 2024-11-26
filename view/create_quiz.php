<?php
session_start();
require_once '../functions/auth_functions.php';

// Ensure only teachers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Helper function to create question type options
function getQuestionTypeOptions() {
    $types = [
        'true_false' => 'True/False',
        'multiple_choice' => 'Multiple Choice',
        'multiple_answer' => 'Multiple Answer',
        'short_answer' => 'Short Answer'
    ];
    
    $html = '<option value="">Select question type...</option>';
    foreach ($types as $value => $label) {
        $html .= "<option value=\"{$value}\">{$label}</option>";
    }
    return $html;
}

// Helper function to create true/false options
function createTrueFalseOptions($questionId) {
    return "
        <div class='true-false-options'>
            <label class='option-card'>
                <input type='radio' name='correct_{$questionId}' value='true' required>
                <span class='option-text'>True</span>
                <span class='checkmark'></span>
            </label>
            <label class='option-card'>
                <input type='radio' name='correct_{$questionId}' value='false' required>
                <span class='option-text'>False</span>
                <span class='checkmark'></span>
            </label>
        </div>
    ";
}

include_once '../components/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/create_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="quiz-creator">
        <form id="quizForm" method="POST" action="../actions/save_quiz.php">
            <div class="quiz-header">
                <input type="text" name="quiz_title" class="quiz-title" placeholder="Untitled Quiz" required>
                <input type="text" name="quiz_description" class="quiz-description" placeholder="Quiz description">
                
                <div class="quiz-settings">
                    <div class="date-time-picker">
                        <div class="setting-group">
                            <label>Due Date:</label>
                            <input type="date" name="quiz_due_date" class="date-picker">
                        </div>
                        <div class="setting-group">
                            <label>Due Time:</label>
                            <input type="time" name="quiz_due_time" class="time-picker">
                        </div>
                    </div>
                    <div class="setting-group">
                        <label>Quiz Mode:</label>
                        <select name="quiz_mode">
                            <option value="individual">Individual</option>
                            <option value="group">Group</option>
                            <option value="live">Live Quiz</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="questionsContainer" class="questions-container">
                <!-- Questions will be added here -->
            </div>

            <div class="action-buttons">
                <button type="button" onclick="addQuestion()" class="btn-add">
                    <i class='bx bx-plus'></i> Add Question
                </button>
                <button type="submit" class="btn-save">
                    <i class='bx bx-save'></i> Save Quiz
                </button>
            </div>
        </form>
    </div>

    <script>
        let questionCount = 0;

        function addQuestion() {
            questionCount++;
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-card';
            questionDiv.innerHTML = `
                <div class="question-header">
                    <h3>Question ${questionCount}</h3>
                    <button type="button" class="delete-question" onclick="deleteQuestion(this)">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="questions[${questionCount}][text]" class="question-text" 
                             placeholder="Enter your question here..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Question Type</label>
                    <select name="questions[${questionCount}][type]" 
                            class="question-type" onchange="handleQuestionTypeChange(this)">
                        <?php echo getQuestionTypeOptions(); ?>
                    </select>
                </div>
                <div class="options-container">
                    <!-- Options will be added here based on question type -->
                </div>
            `;
            
            document.getElementById('questionsContainer').appendChild(questionDiv);
        }

        function handleQuestionTypeChange(select) {
            const optionsContainer = select.closest('.question-card').querySelector('.options-container');
            const questionId = select.closest('.question-card').querySelector('.question-text').name.match(/\d+/)[0];

            switch(select.value) {
                case 'true_false':
                    optionsContainer.innerHTML = <?php echo json_encode(createTrueFalseOptions('${questionId}')); ?>;
                    break;
                case 'multiple_choice':
                    createMultipleChoiceOptions(optionsContainer, questionId);
                    break;
                case 'multiple_answer':
                    createMultipleAnswerOptions(optionsContainer, questionId);
                    break;
                case 'short_answer':
                    createShortAnswerField(optionsContainer, questionId);
                    break;
            }
        }

        // Include only the necessary JavaScript functions
        <?php include '../assets/js/quiz_functions.js'; ?>
    </script>
</body>
</html>