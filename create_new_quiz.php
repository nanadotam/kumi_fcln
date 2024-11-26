<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
</head>
<body>
    <h1>Create New Quiz</h1>
    <form action="insert_quiz.php" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="created_by">Created By (User ID):</label>
        <input type="number" id="created_by" name="created_by" required><br><br>

        <label for="mode">Mode:</label>
        <select id="mode" name="mode" required>
            <option value="individual">Individual</option>
            <option value="group">Group</option>
            <option value="asynchronous">Asynchronous</option>
            <option value="live">Live</option>
        </select><br><br>

        <label for="deadline">Deadline:</label>
        <input type="datetime-local" id="deadline" name="deadline" required><br><br>

        <input type="submit" value="Create Quiz">
    </form>
</body>
</html>

<?php
// Only process form data if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kumidb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validate and sanitize inputs
    $title = !empty($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
    $description = !empty($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $created_by = !empty($_POST['created_by']) ? (int)$_POST['created_by'] : 0;
    $mode = !empty($_POST['mode']) ? $conn->real_escape_string($_POST['mode']) : '';
    $deadline = !empty($_POST['deadline']) ? $conn->real_escape_string($_POST['deadline']) : '';
    $created_at = date('Y-m-d H:i:s');

    // Validate required fields
    if (empty($title) || empty($description) || empty($created_by) || empty($mode) || empty($deadline)) {
        die("All fields are required");
    }

    $sql = "INSERT INTO Quizzes (title, description, created_by, mode, deadline, created_at) 
            VALUES ('$title', '$description', $created_by, '$mode', '$deadline', '$created_at')";

    if ($conn->query($sql) === TRUE) {
        $quiz_id = $conn->insert_id;
        echo "New quiz created successfully with ID: " . $quiz_id . "<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Insert answers
    $question_ids = $_POST['question_id'];
    $answer_texts = $_POST['answer_text'];
    $is_corrects = isset($_POST['is_correct']) ? $_POST['is_correct'] : [];
    $model_answers = $_POST['model_answer'];

    for ($i = 0; $i < count($question_ids); $i++) {
        $question_id = $question_ids[$i];
        $answer_text = $answer_texts[$i];
        $is_correct = isset($is_corrects[$i]) ? 1 : 0;
        $model_answer = $model_answers[$i];

        $sql = "INSERT INTO Answers (question_id, answer_text, is_correct, model_answer) 
                VALUES ('$question_id', '$answer_text', '$is_correct', '$model_answer')";

        if ($conn->query($sql) === TRUE) {
            echo "New answer created successfully<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>