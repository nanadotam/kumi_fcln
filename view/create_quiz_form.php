<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New Quiz</title>
    <link rel="stylesheet" href="../assets/css/create_quiz.css">
</head>
<body>
    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Create New Quiz</h1>
            <form id="quiz-form" method="post">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea><br><br>

                <label for="mode">Mode:</label>
                <select id="mode" name="mode" required>
                    <option value="individual">Individual</option>
                    <option value="group">Group</option>
                    <option value="live">Live</option>
                    <option value="asynchronous">Asynchronous</option>
                </select><br><br>

                <label for="deadline">Deadline:</label>
                <input type="datetime-local" id="deadline" name="deadline"><br><br>

                <h2>Quiz Settings</h2>
                <label for="shuffle_questions">Shuffle Questions:</label>
                <input type="checkbox" id="shuffle_questions" name="shuffle_questions"><br><br>

                <label for="shuffle_answers">Shuffle Answers:</label>
                <input type="checkbox" id="shuffle_answers" name="shuffle_answers"><br><br>

                <label for="max_attempts">Maximum Attempts:</label>
                <input type="number" id="max_attempts" name="max_attempts" min="1"><br><br>

                <label for="time_limit">Time Limit (minutes):</label>
                <input type="number" id="time_limit" name="time_limit" min="0"><br><br>

                <section class="questions-section">
                    <h2>Questions</h2>
                    <div id="questions"></div>
                    <button type="button" class="btn btn-secondary btn-add-question" onclick="addQuestion()">
                        <i class='bx bx-plus'></i>
                        Add Question
                    </button>
                </section>

                <div class="button-container">
                    <button type="button" class="btn btn-secondary">
                        <i class='bx bx-save'></i>
                        Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check'></i>
                        Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let questionCount = 1;

        function addQuestion() {
            const questionsDiv = document.getElementById('questions');
            const newQuestionDiv = document.createElement('div');
            newQuestionDiv.classList.add('question');
            newQuestionDiv.innerHTML = `
                <label for="question_text">Question Text:</label>
                <textarea id="question_text" name="questions[${questionCount}][question_text]" required></textarea><br><br>

                <label for="type">Type:</label>
                <select id="type" name="questions[${questionCount}][type]" required onchange="updateAnswers(${questionCount})">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="multiple_answer">Multiple Answer</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                </select><br><br>

                <label for="points">Points:</label>
                <input type="number" id="points" name="questions[${questionCount}][points]" required><br><br>

                <h3>Answers</h3>
                <div class="answers" id="answers_${questionCount}">
                    <div class="answer">
                        <label for="answer_text">Answer Text:</label>
                        <textarea id="answer_text" name="questions[${questionCount}][answers][0][answer_text]" required></textarea><br><br>

                        <label for="is_correct">Is Correct:</label>
                        <input type="radio" id="is_correct" name="questions[${questionCount}][answers][is_correct]" value="0"><br><br>

                        <label for="model_answer">Model Answer:</label>
                        <textarea id="model_answer" name="questions[${questionCount}][answers][0][model_answer]"></textarea><br><br>
                    </div>
                </div>
            `;
            questionsDiv.appendChild(newQuestionDiv);
            questionCount++;
        }

        function updateAnswers(questionIndex) {
            const typeSelect = document.querySelector(`select[name="questions[${questionIndex}][type]"]`);
            const answersDiv = document.getElementById(`answers_${questionIndex}`);
            answersDiv.innerHTML = '';

            if (typeSelect.value === 'multiple_choice') {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <button type="button" onclick="addOption(${questionIndex})">Add Option</button><br><br>
                        <div class="option">
                            <label for="answer_text">Option 1:</label>
                            <textarea id="answer_text" name="questions[${questionIndex}][answers][0][answer_text]" required></textarea>
                            <input type="radio" id="is_correct" name="questions[${questionIndex}][answers][is_correct]" value="0"><br><br>
                        </div>
                    </div>
                `;
            } else if (typeSelect.value === 'multiple_answer') {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <button type="button" onclick="addOption(${questionIndex})">Add Option</button><br><br>
                        <div class="option">
                            <label for="answer_text">Option 1:</label>
                            <textarea id="answer_text" name="questions[${questionIndex}][answers][0][answer_text]" required></textarea>
                            <input type="checkbox" id="is_correct" name="questions[${questionIndex}][answers][0][is_correct]" value="1"><br><br>
                        </div>
                    </div>
                `;
            } else if (typeSelect.value === 'true_false') {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label for="answer_text">True</label>
                        <input type="radio" id="is_correct" name="questions[${questionIndex}][answers][is_correct]" value="1"><br><br>

                        <label for="answer_text">False</label>
                        <input type="radio" id="is_correct" name="questions[${questionIndex}][answers][is_correct]" value="0"><br><br>
                    </div>
                `;
            } else {
                answersDiv.innerHTML = `
                    <div class="answer">
                        <label for="answer_text">Answer Text:</label>
                        <textarea id="answer_text" name="questions[${questionIndex}][answers][0][answer_text]" required></textarea><br><br>

                        <label for="is_correct">Is Correct:</label>
                        <input type="checkbox" id="is_correct" name="questions[${questionIndex}][answers][0][is_correct]" value="1"><br><br>

                        <label for="model_answer">Model Answer:</label>
                        <textarea id="model_answer" name="questions[${questionIndex}][answers][0][model_answer]"></textarea><br><br>
                    </div>
                `;
            }
        }

        function addOption(questionIndex) {
            const answersDiv = document.getElementById(`answers_${questionIndex}`);
            const optionsCount = answersDiv.getElementsByClassName('option').length;
            const newOption = document.createElement('div');
            newOption.className = 'option';
            
            const type = document.querySelector(`select[name="questions[${questionIndex}][type]"]`).value;
            const inputType = type === 'multiple_choice' ? 'radio' : 'checkbox';
            
            newOption.innerHTML = `
                <label for="answer_text">Option ${optionsCount + 1}:</label>
                <textarea id="answer_text" name="questions[${questionIndex}][answers][${optionsCount}][answer_text]" required></textarea>
                <input type="${inputType}" id="is_correct" name="questions[${questionIndex}][answers][${optionsCount}][is_correct]" value="1"><br><br>
            `;
            
            answersDiv.querySelector('.answer').appendChild(newOption);
        }

        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.classList.add('loading');
            
            const formData = new FormData(this);
            
            fetch('create_quiz.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response status: ' + response.status + ' ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Success: ' + data.message);
                    window.location.href = 'quiz.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error details: ' + error.message);
            })
            .finally(() => {
                submitButton.classList.remove('loading');
            });
        });
    </script>
</body>
</html> 