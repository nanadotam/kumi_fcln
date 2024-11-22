<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';
require_once '../functions/student_functions.php';

$currentPage = 'dashboard';
include_once '../components/sidebar.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];

// Fetch all required data
// $availableQuizzes = getAvailableQuizzes($studentId);
$completedQuizzes = getCompletedQuizzes($studentId);
// $progress = getStudentProgress($studentId);
// $upcomingDeadlines = getUpcomingDeadlines($studentId);

// Get only recent completed quizzes (last 5)
// $recentCompletedQuizzes = array_slice($completedQuizzes, 0, 5);

// Calculate additional stats for display
$totalQuizzes = $progress['total_quizzes'] ?? 0;
$completedQuizCount = $progress['completed_quizzes'] ?? 0;
$completionRate = $totalQuizzes > 0 ? round(($completedQuizCount / $totalQuizzes) * 100) : 0;
$averageScore = round($progress['average_score'] ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Kumi</title>

    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">

    <div class = "quizcode-container"> 
        <h2>Ready for your next quiz?</h2>
        <p>Enter the quiz code to start </p> 
        <input type="text" name="quizcode" id="quizcode" placeholder="Enter Quiz Code">
        <button>Start Quiz</button>

    </div> 
        <div class="welcome-container">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Student') ?>!</h1>
            <p>Here's your learning progress:</p>
            
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3> Completion Rate </h3>
                    <p class="stat-number">
                        <?= $completionRate ?>%
                    </p>
                </div>
                <div class="stat-box">
                    <h3>Average Score</h3>
                    <p class="stat-number"><?= $averageScore ?>%</p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section-card">
                <h2>Recent Scores</h2>
                <div class="quiz-list">
                    <?php if (!empty($recentCompletedQuizzes)): ?>
                        <?php foreach ($recentCompletedQuizzes as $quiz): ?>
                            <div class="quiz-item">
                                <div>
                                    <h4><?= htmlspecialchars($quiz['title']) ?></h4>
                                    <small><?= date('M d, Y', strtotime($quiz['submitted_at'])) ?></small>
                                </div>
                                <span class="score-badge <?= $quiz['score'] >= 70 ? 'passing' : 'failing' ?>">
                                    <?= $quiz['score'] ?>%
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent scores available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- <div class="section-card">
                <h2>Upcoming Quizzes</h2>
                <div class="quiz-list">
                    <?php if (!empty($upcomingDeadlines)): ?>
                        <?php foreach ($upcomingDeadlines as $deadline): ?>
                            <div class="quiz-item">
                                <div>
                                    <h4><?= htmlspecialchars($deadline['title']) ?></h4>
                                    <small>Due: <?= date('M d, Y', strtotime($deadline['deadline'])) ?></small>
                                </div>
                                <a href="take_quiz.php?id=<?= $deadline['quiz_id'] ?>" class="start-quiz-btn">Start Quiz</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming quizzes at the moment.</p>
                    <?php endif; ?>
                </div>
            </div> -->

            <!-- <section class="completed-quizzes">
                <h2>Completed Quizzes</h2>
                <?php if (empty($completedQuizzes)): ?>
                    <p class="no-quizzes">You haven't completed any quizzes yet.</p>
                <?php else: ?>
                    <div class="quiz-grid">
                        <?php foreach ($completedQuizzes as $quiz): ?>
                            <div class="quiz-card <?= $quiz['performance'] ?>">
                                <div class="quiz-header">
                                    <h3><?= $quiz['title'] ?></h3>
                                    <span class="score-badge">
                                        <?= number_format($quiz['score'], 1) ?>%
                                    </span>
                                </div>
                                <div class="quiz-stats">
                                    <span>
                                        <i class='bx bx-check-circle'></i>
                                        <?= $quiz['correct_answers'] ?>/<?= $quiz['total_questions'] ?> Correct
                                    </span>
                                    <span>
                                        <i class='bx bx-time'></i>
                                        <?= date('M d, Y', strtotime($quiz['submitted_at'])) ?>
                                    </span>
                                </div>
                                <a href="quiz_result.php?id=<?= $quiz['result_id'] ?>" class="view-result-btn">
                                    View Details
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section> -->
        </div>
    </div>

    <script>
    document.getElementById('quizCodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../actions/validate_quiz_code.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            // Show error message
            const input = document.getElementById('quizcode');
            input.classList.add('error');
            
            // Create or update error message
            let errorMsg = document.querySelector('.error-message');
            if (!errorMsg) {
                errorMsg = document.createElement('p');
                errorMsg.className = 'error-message';
                input.parentNode.insertBefore(errorMsg, input.nextSibling);
            }
            errorMsg.textContent = data.message;
            
            // Clear error state after 3 seconds
            setTimeout(() => {
                input.classList.remove('error');
                errorMsg.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

</body>

</html>
