<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/user_functions.php';
require_once '../functions/quiz_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Get user data and statistics
try {
    $db = Database::getInstance();
    
    // Get basic user info
    $sql = "SELECT 
            u.first_name,
            u.last_name,
            u.email";
    
    // Add role-specific statistics
    if ($userRole === 'student') {
        $sql .= ", COUNT(DISTINCT qr.quiz_id) as total_quizzes,
                  ROUND(AVG(qr.score), 1) as average_score,
                  COUNT(CASE WHEN qr.score >= 70 THEN 1 END) as quizzes_passed";
    } else {
        $sql .= ", COUNT(DISTINCT q.quiz_id) as total_quizzes_created,
                  (SELECT COUNT(DISTINCT qr.user_id) 
                   FROM Quizzes q2 
                   LEFT JOIN QuizResults qr ON q2.quiz_id = qr.quiz_id 
                   WHERE q2.created_by = u.user_id) as total_students,
                  (SELECT COUNT(*) 
                   FROM QuizResults qr 
                   JOIN Quizzes q2 ON qr.quiz_id = q2.quiz_id 
                   WHERE q2.created_by = u.user_id) as total_attempts";
    }
    
    $sql .= " FROM Users u";
    
    if ($userRole === 'student') {
        $sql .= " LEFT JOIN QuizResults qr ON u.user_id = qr.user_id";
    } else {
        $sql .= " LEFT JOIN Quizzes q ON u.user_id = q.created_by";
    }
    
    $sql .= " WHERE u.user_id = ? GROUP BY u.user_id";
            
    $result = $db->query($sql, [$userId]);
    $userData = $result->fetch_assoc();

} catch(Exception $e) {
    $_SESSION['error'] = "Error fetching user data: " . $e->getMessage();
    $userData = [];
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            throw new Exception('Both password fields are required');
        }
        
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            throw new Exception('Passwords do not match');
        }
        
        if (strlen($_POST['new_password']) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }
        
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET password = ? WHERE user_id = ?";
        $db->query($sql, [$hashedPassword, $userId]);
        
        $_SESSION['message'] = 'Password updated successfully!';
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Kumi</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include_once '../components/sidebar.php'; ?>
    
    <main class="profile-page">
        <div class="profile-header">
            <h1><?= ucfirst($userRole) ?> Profile</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
        </div>

        <div class="profile-content">
            <div class="user-info">
                <div class="info-card">
                    <h2>Personal Information</h2>
                    <div class="info-item">
                        <label>Name:</label>
                        <span><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?= htmlspecialchars($userData['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Role:</label>
                        <span><?= ucfirst($userRole) ?></span>
                    </div>
                </div>

                <div class="password-section">
                    <h2>Change Password</h2>
                    <form action="profile.php" method="POST" class="password-form">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <span class="error" id="passwordError"></span>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <span class="error" id="confirmPasswordError"></span>
                        </div>
                        <button type="submit" class="save-btn">Update Password</button>
                    </form>
                </div>
            </div>

            <div class="stats-section">
                <h2><?= ucfirst($userRole) ?> Statistics</h2>
                <div class="stats-grid">
                    <?php if ($userRole === 'student'): ?>
                        <div class="stat-card">
                            <i class='bx bx-book-open'></i>
                            <div class="stat-info">
                                <h3>Quizzes Taken</h3>
                                <p><?= $userData['total_quizzes'] ?? 0 ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class='bx bx-trophy'></i>
                            <div class="stat-info">
                                <h3>Average Score</h3>
                                <p><?= $userData['average_score'] ?? 0 ?>%</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class='bx bx-check-circle'></i>
                            <div class="stat-info">
                                <h3>Quizzes Passed</h3>
                                <p><?= $userData['quizzes_passed'] ?? 0 ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="stat-card">
                            <i class='bx bx-book-content'></i>
                            <div class="stat-info">
                                <h3>Quizzes Created</h3>
                                <p><?= $userData['total_quizzes_created'] ?? 0 ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class='bx bx-group'></i>
                            <div class="stat-info">
                                <h3>Total Students</h3>
                                <p><?= $userData['total_students'] ?? 0 ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class='bx bx-chart'></i>
                            <div class="stat-info">
                                <h3>Total Attempts</h3>
                                <p><?= $userData['total_attempts'] ?? 0 ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/profile.js"></script>
</body>
</html>