<?php
session_start();


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kumidb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Please log in first");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert quiz data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];
    $mode = $_POST['mode'];
    $deadline = $_POST['deadline'];
    $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
    $shuffle_answers = isset($_POST['shuffle_answers']) ? 1 : 0;
    $max_attempts = !empty($_POST['max_attempts']) ? $_POST['max_attempts'] : NULL;
    $time_limit = !empty($_POST['time_limit']) ? $_POST['time_limit'] : NULL;

    // Create a prepared statement
    $stmt = $conn->prepare("INSERT INTO Quizzes (title, description, created_by, mode, deadline, 
            shuffle_questions, shuffle_answers, max_attempts, time_limit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param("ssissiiis", $title, $description, $created_by, $mode, $deadline, 
                      $shuffle_questions, $shuffle_answers, $max_attempts, $time_limit);
    
    // Execute the statement
    if ($stmt->execute()) {
        $quiz_id = $conn->insert_id;

        // Insert questions and answers
        foreach ($_POST['questions'] as $question) {
            $question_text = $question['question_text'];
            $type = $question['type'];
            $points = $question['points'];
            $model_answer = isset($question['model_answer']) ? $question['model_answer'] : NULL;
            $order_position = NULL;

            // Insert question using prepared statement
            $stmt = $conn->prepare("INSERT INTO Questions (quiz_id, question_text, type, points, model_answer, order_position) 
                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issisi", $quiz_id, $question_text, $type, $points, $model_answer, $order_position);
            
            if ($stmt->execute()) {
                $question_id = $conn->insert_id;
                $stmt->close();

                // Handle true/false questions differently
                if ($type === 'true_false') {
                    // Insert True option
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, 'True', ?, ?)");
                    $true_is_correct = isset($question['answers']['is_correct']) && $question['answers']['is_correct'] == '1' ? 1 : 0;
                    $order_position = 1;
                    $stmt_answer->bind_param("iii", $question_id, $true_is_correct, $order_position);
                    $stmt_answer->execute();
                    $stmt_answer->close();

                    // Insert False option
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, 'False', ?, ?)");
                    $false_is_correct = $true_is_correct ? 0 : 1;
                    $order_position = 2;
                    $stmt_answer->bind_param("iii", $question_id, $false_is_correct, $order_position);
                    $stmt_answer->execute();
                    $stmt_answer->close();
                } else {
                    // Handle other question types as before
                    $stmt_answer = $conn->prepare("INSERT INTO Answers (question_id, answer_text, is_correct, order_position) 
                            VALUES (?, ?, ?, ?)");
                            
                    foreach ($question['answers'] as $answer) {
                        $answer_text = $answer['answer_text'];
                        $is_correct = isset($answer['is_correct']) ? 1 : 0;
                        $order_position = NULL;

                        $stmt_answer->bind_param("isii", $question_id, $answer_text, $is_correct, $order_position);
                        $stmt_answer->execute();
                    }
                    
                    $stmt_answer->close();
                }
            } else {
                $stmt->close();
            }
        }
        echo "New quiz created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New Quiz</title>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Create New Quiz</h1>
            <form id="quiz-form" method="post" action="">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br><br>
        <!-- Import CSS -->
        <link rel="stylesheet" href="new_quiz.css">

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
            
            fetch('process_quiz.php', {  // Point to the new processing file
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
                    window.location.href = '../view/quiz.php';
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

