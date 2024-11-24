<?php
require_once '../includes/auth.php';
requireTeacher(); // Only teachers can access this test interface

$quizId = $_GET['quiz_id'] ?? null;
if (!$quizId) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Quiz Test Interface - Kumi</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/live-quiz.css">
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="quiz-test-page">
        <div class="test-container">
            <h1>Live Quiz Test Interface</h1>
            <div class="quiz-info">
                <h2>Quiz Code: <span id="quizCode">Loading...</span></h2>
                <p>Use this interface to simulate a live quiz session</p>
            </div>

            <div class="test-controls">
                <button onclick="simulateStudent()" class="btn-simulate">
                    <i class='bx bx-user-plus'></i> Add Test Student
                </button>
                <button onclick="startQuiz()" class="btn-start">
                    <i class='bx bx-play'></i> Start Quiz
                </button>
                <button onclick="nextQuestion()" class="btn-next">
                    <i class='bx bx-skip-next'></i> Next Question
                </button>
            </div>

            <div class="simulation-status">
                <h3>Active Students</h3>
                <div id="studentList" class="student-list">
                    <!-- Students will be listed here -->
                </div>
            </div>

            <div class="live-leaderboard">
                <h3>Live Leaderboard</h3>
                <div id="leaderboard" class="leaderboard-list">
                    <!-- Leaderboard will update here -->
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/live-quiz.js"></script>
    <script>
        // Initialize the test controller
        const quizController = new LiveQuizController('<?php echo $quizId; ?>');
        
        // Update UI with quiz code
        document.getElementById('quizCode').textContent = quizController.quizCode;
    </script>
</body>
</html> 