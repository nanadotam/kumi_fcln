<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/quiz_functions.php';

$currentPage = 'dashboard';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];

// Get user data and statistics
try {
    $db = Database::getInstance();

    // Reuse the statistics query from profile.php
    $sql = "SELECT 
            COUNT(DISTINCT qr.quiz_id) as total_quizzes,
            ROUND(AVG(qr.score), 1) as average_score,
            COUNT(CASE WHEN qr.score >= 70 THEN 1 END) as quizzes_passed
            FROM Users u
            LEFT JOIN QuizResults qr ON u.user_id = qr.user_id
            WHERE u.user_id = ?
            GROUP BY u.user_id";

    $result = $db->query($sql, [$studentId]);
    $stats = $result->fetch_assoc();

    // Set the variables using the query results
    $totalQuizzes = $stats['total_quizzes'] ?? 0;
    $averageScore = $stats['average_score'] ?? 0;
    $quizzesPassed = $stats['quizzes_passed'] ?? 0;

} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching statistics: " . $e->getMessage();
    $stats = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Kumi</title>
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <div class="dashboard-container">
        <section class="welcome-section">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Student') ?>!</h1>
            <div class="stats-overview">
                <div class="stat-card">
                    <i class='bx bx-book-open'></i>
                    <div class="stat-info">
                        <h3>Quizzes Taken</h3>
                        <p><?= $totalQuizzes ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class='bx bx-task'></i>
                    <div class="stat-info">
                        <h3>Average Score</h3>
                        <p><?= $averageScore ?>%</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class='bx bx-check-circle'></i>
                    <div class="stat-info">
                        <h3>Quizzes Passed</h3>
                        <p><?= $quizzesPassed ?></p>
                    </div>
                </div>
            </div>
        </section>

        <div class="quizcode-container">
            <h2>Ready for your next quiz?</h2>
            <p>Enter the quiz code to start</p>
            <input type="text" name="quizcode" id="quizcode" placeholder="Enter Quiz Code">
            <button class="start-btn">Start Quiz</button>
        </div>
    </div>
</body>
</html>
