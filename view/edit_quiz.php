<?php
session_start();
require_once '../utils/Database.php';

try {
    $db = Database::getInstance();
    
    if (!isset($_SESSION['user_id'])) {
        die("Please log in first");
    }

    // Get quiz ID from URL
    $quiz_id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$quiz_id) {
        die("No quiz specified");
    }

    // Fetch quiz data
    $result = $db->query("SELECT * FROM Quizzes WHERE quiz_id = ?", [$quiz_id]);
    $quiz = $result->fetch_assoc();

    if (!$quiz) {
        die("Quiz not found");
    }

    // Fetch questions
    $result = $db->query(
        "SELECT * FROM Questions WHERE quiz_id = ? ORDER BY order_position",
        [$quiz_id]
    );
    $questions = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch answers for each question
    foreach ($questions as &$question) {
        $result = $db->query(
            "SELECT * FROM Answers WHERE question_id = ? ORDER BY order_position",
            [$question['question_id']]
        );
        $question['answers'] = $result->fetch_all(MYSQLI_ASSOC);
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/create_quiz.css">
    <link rel="stylesheet" href="../assets/css/edit_quiz.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="edit-mode-badge">
                <i class='bx bx-pencil'></i> Edit Mode
            </div>
            
            <h1>Edit Quiz</h1>
            <form id="quiz-form" method="post" action="../actions/update_quiz.php">
                <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($quiz['description']) ?></textarea>

                <label for="mode">Mode:</label>
                <select id="mode" name="mode" required>
                    <option value="individual" <?= $quiz['mode'] === 'individual' ? 'selected' : '' ?>>Individual</option>
                    <option value="group" <?= $quiz['mode'] === 'group' ? 'selected' : '' ?>>Group</option>
                    <option value="live" <?= $quiz['mode'] === 'live' ? 'selected' : '' ?>>Live</option>
                    <option value="asynchronous" <?= $quiz['mode'] === 'asynchronous' ? 'selected' : '' ?>>Asynchronous</option>
                </select>

                <label for="deadline">Deadline:</label>
                <input type="datetime-local" id="deadline" name="deadline" 
                       value="<?= date('Y-m-d\TH:i', strtotime($quiz['deadline'])) ?>">

                <h2>Quiz Settings</h2>
                <label for="shuffle_questions">Shuffle Questions:</label>
                <input type="checkbox" id="shuffle_questions" name="shuffle_questions" 
                       <?= $quiz['shuffle_questions'] ? 'checked' : '' ?>>

                <label for="shuffle_answers">Shuffle Answers:</label>
                <input type="checkbox" id="shuffle_answers" name="shuffle_answers"
                       <?= $quiz['shuffle_answers'] ? 'checked' : '' ?>>

                <label for="max_attempts">Maximum Attempts:</label>
                <input type="number" id="max_attempts" name="max_attempts" 
                       value="<?= $quiz['max_attempts'] ?>" min="1">

                <label for="time_limit">Time Limit (minutes):</label>
                <input type="number" id="time_limit" name="time_limit" 
                       value="<?= $quiz['time_limit'] ?>" min="0">

                <section class="questions-section">
                    <h2>Questions</h2>
                    <div id="questions">
                        <?php foreach ($questions as $index => $question): ?>
                            <!-- Existing questions will be loaded here -->
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary btn-add-question" onclick="addQuestion()">
                        <i class='bx bx-plus'></i> Add Question
                    </button>
                </section>

                <div class="button-container">
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check'></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize with existing questions
        let questions = <?= json_encode($questions) ?>;
        let questionCount = questions.length;

        // Load existing questions
        window.onload = function() {
            questions.forEach((question, index) => {
                loadExistingQuestion(question, index);
            });
        };

        function loadExistingQuestion(question, index) {
            const questionsDiv = document.getElementById('questions');
            const newQuestionDiv = document.createElement('div');
            newQuestionDiv.classList.add('question-card');
            
            newQuestionDiv.innerHTML = `
                <div class="question-header">
                    <h3>Question ${index + 1}</h3>
                    <button type="button" class="delete-question" onclick="deleteQuestion(this)">
                        <i class='bx bx-trash'></i> Delete
                    </button>
                </div>

                <label for="question_text_${index}">Question Text:</label>
                <textarea id="question_text_${index}" 
                    name="questions[${index}][question_text]" 
                    required>${question.question_text}</textarea>

                <label for="type_${index}">Type:</label>
                <select id="type_${index}" 
                    name="questions[${index}][type]" 
                    required 
                    onchange="updateAnswers(${index})">
                    <option value="multiple_choice" ${question.type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                    <option value="multiple_answer" ${question.type === 'multiple_answer' ? 'selected' : ''}>Multiple Answer</option>
                    <option value="true_false" ${question.type === 'true_false' ? 'selected' : ''}>True/False</option>
                    <option value="short_answer" ${question.type === 'short_answer' ? 'selected' : ''}>Short Answer</option>
                </select>

                <label for="points_${index}">Points:</label>
                <input type="number" 
                    id="points_${index}" 
                    name="questions[${index}][points]" 
                    value="${question.points}" 
                    required>

                <div class="answers" id="answers_${index}"></div>
            `;

            questionsDiv.appendChild(newQuestionDiv);
            
            // Load existing answers
            loadExistingAnswers(question.answers, index, question.type);
        }

        function loadExistingAnswers(answers, questionIndex, questionType) {
            const answersDiv = document.getElementById(`answers_${questionIndex}`);
            answersDiv.innerHTML = '';

            if (questionType === 'true_false') {
                // Handle True/False questions
                const trueAnswer = answers.find(a => a.answer_text === 'True');
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label>True</label>
                        <input type="radio" 
                            name="questions[${questionIndex}][answers][is_correct]" 
                            value="1" 
                            ${trueAnswer && trueAnswer.is_correct ? 'checked' : ''}>
                        
                        <label>False</label>
                        <input type="radio" 
                            name="questions[${questionIndex}][answers][is_correct]" 
                            value="0" 
                            ${!trueAnswer || !trueAnswer.is_correct ? 'checked' : ''}>
                    </div>
                `;
            } else if (questionType === 'short_answer') {
                // Handle Short Answer questions
                const answer = answers[0] || { answer_text: '', model_answer: '' };
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label for="model_answer_${questionIndex}">Model Answer:</label>
                        <textarea id="model_answer_${questionIndex}" 
                            name="questions[${questionIndex}][model_answer]">${answer.model_answer || ''}</textarea>
                    </div>
                `;
            } else {
                // Handle Multiple Choice/Answer questions
                answersDiv.innerHTML = `
                    <button type="button" onclick="addOption(${questionIndex})">Add Option</button>
                `;

                answers.forEach((answer, answerIndex) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'option';
                    optionDiv.innerHTML = `
                        <label>Option ${answerIndex + 1}:</label>
                        <textarea name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                            required>${answer.answer_text}</textarea>
                        <input type="${questionType === 'multiple_choice' ? 'radio' : 'checkbox'}" 
                            name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" 
                            value="1" 
                            ${answer.is_correct ? 'checked' : ''}>
                    `;
                    answersDiv.appendChild(optionDiv);
                });
            }
        }

        function addQuestion() {
            const questionsDiv = document.getElementById('questions');
            const newQuestionDiv = document.createElement('div');
            newQuestionDiv.classList.add('question-card');
            
            newQuestionDiv.innerHTML = `
                <div class="question-header">
                    <h3>Question ${questionCount + 1}</h3>
                    <button type="button" class="delete-question" onclick="deleteQuestion(this)">
                        <i class='bx bx-trash'></i> Delete
                    </button>
                </div>

                <label for="question_text_${questionCount}">Question Text:</label>
                <textarea id="question_text_${questionCount}" 
                    name="questions[${questionCount}][question_text]" 
                    required></textarea>

                <label for="type_${questionCount}">Type:</label>
                <select id="type_${questionCount}" 
                    name="questions[${questionCount}][type]" 
                    required 
                    onchange="updateAnswers(${questionCount})">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="multiple_answer">Multiple Answer</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                </select>

                <label for="points_${questionCount}">Points:</label>
                <input type="number" 
                    id="points_${questionCount}" 
                    name="questions[${questionCount}][points]" 
                    required>

                <div class="answers" id="answers_${questionCount}"></div>
            `;

            questionsDiv.appendChild(newQuestionDiv);
            updateAnswers(questionCount);
            questionCount++;
        }

        function deleteQuestion(button) {
            if (confirm('Are you sure you want to delete this question?')) {
                button.closest('.question-card').remove();
            }
        }

        function updateAnswers(questionIndex) {
            const typeSelect = document.querySelector(`select[name="questions[${questionIndex}][type]"]`);
            const answersDiv = document.getElementById(`answers_${questionIndex}`);
            const type = typeSelect.value;

            answersDiv.innerHTML = '';

            if (type === 'true_false') {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label>True</label>
                        <input type="radio" 
                            name="questions[${questionIndex}][answers][is_correct]" 
                            value="1">
                        
                        <label>False</label>
                        <input type="radio" 
                            name="questions[${questionIndex}][answers][is_correct]" 
                            value="0">
                    </div>
                `;
            } else if (type === 'short_answer') {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label for="model_answer_${questionIndex}">Model Answer:</label>
                        <textarea id="model_answer_${questionIndex}" 
                            name="questions[${questionIndex}][model_answer]"></textarea>
                    </div>
                `;
            } else {
                answersDiv.innerHTML = `
                    <button type="button" onclick="addOption(${questionIndex})">Add Option</button>
                `;
                addOption(questionIndex);
            }
        }

        function addOption(questionIndex) {
            const answersDiv = document.getElementById(`answers_${questionIndex}`);
            const optionsCount = answersDiv.getElementsByClassName('option').length;
            const newOption = document.createElement('div');
            newOption.className = 'option';
            
            const type = document.querySelector(`select[name="questions[${questionIndex}][type]"]`).value;
            const inputType = type === 'multiple_choice' ? 'radio' : 'checkbox';
            const name = type === 'multiple_choice' 
                ? `questions[${questionIndex}][answers][is_correct]`
                : `questions[${questionIndex}][answers][${optionsCount}][is_correct]`;
            
            newOption.innerHTML = `
                <label for="answer_text_${questionIndex}_${optionsCount}">Option ${optionsCount + 1}:</label>
                <textarea id="answer_text_${questionIndex}_${optionsCount}" 
                         name="questions[${questionIndex}][answers][${optionsCount}][answer_text]" 
                         required></textarea>
                <input type="${inputType}" 
                       id="is_correct_${questionIndex}_${optionsCount}" 
                       name="${name}" 
                       value="1">
                <label for="is_correct_${questionIndex}_${optionsCount}">Correct Answer</label><br><br>
            `;
            
            answersDiv.querySelector('.answer').appendChild(newOption);
        }
    </script>
</body>
</html> 