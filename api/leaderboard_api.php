<?php
header('Content-Type: application/json');
require_once '../functions/quiz_functions.php';

$quizCode = $_GET['code'] ?? null;

if (!$quizCode) {
    echo json_encode(['success' => false, 'message' => 'Quiz code required']);
    exit();
}

try {
    // Get leaderboard data
    $rankings = getLeaderboardRankings($quizCode);
    $stats = getLeaderboardStats($quizCode);

    echo json_encode([
        'success' => true,
        'rankings' => $rankings,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 