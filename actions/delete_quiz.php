<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);
$quizId = $data['quiz_id'] ?? null;

if (!$quizId) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz ID is required'
    ]);
    exit;
}

// Database connection
$db = new mysqli("localhost", "root", "", "kumidb");

if ($db->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Verify that the quiz belongs to the current teacher
$stmt = $db->prepare("SELECT created_by FROM Quizzes WHERE quiz_id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

if (!$quiz || $quiz['created_by'] !== $_SESSION['user_id']) {
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to delete this quiz'
    ]);
    exit;
}

// Start transaction
$db->begin_transaction();

try {
    // The foreign key constraints will handle the deletion of related records
    $stmt = $db->prepare("DELETE FROM Quizzes WHERE quiz_id = ? AND created_by = ?");
    $stmt->bind_param("ii", $quizId, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Quiz deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete quiz');
    }

} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$db->close();
?> 