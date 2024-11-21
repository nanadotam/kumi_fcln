<?php
session_start();
require_once '../functions/analytics_functions.php';

$quizId = $_GET['id'] ?? null;
if (!$quizId) {
    header('Location: teacher_dashboard.php');
    exit();
}

$analytics = getQuizAnalytics($quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Analytics</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="analytics-container">
        <h1>Quiz Analytics</h1>
        
        <div class="stats-overview">
            <div class="stat-card">
                <h3>Total Attempts</h3>
                <p><?= $analytics['overall_stats']['total_attempts'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Average Score</h3>
                <p><?= number_format($analytics['overall_stats']['average_score'], 2) ?>%</p>
            </div>
        </div>
        
        <div class="question-analysis">
            <h2>Question Performance</h2>
            <canvas id="questionChart"></canvas>
        </div>
    </div>
    
    <script>
        const questionData = <?= json_encode($analytics['question_stats']) ?>;
        // Chart initialization code here
    </script>
</body>
</html> 