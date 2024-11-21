<?php
session_start();
require_once '../functions/auth_functions.php';
require_once '../functions/user_functions.php';
require_once '../functions/quiz_functions.php';

$currentPage = 'profile';
include_once '../components/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Get user data using the Database class
try {
    $db = Database::getInstance();
    
    // Get basic user data
    $userData = getUserData($userId);

    if ($userRole === 'student') {
        // Get student statistics
        $statsQuery = "SELECT 
            COUNT(DISTINCT qa.quiz_id) as total_quizzes,
            COALESCE(AVG(qa.score), 0) as average_score
            FROM quiz_attempts qa 
            WHERE qa.user_id = ?";
        
        $result = $db->query($statsQuery, [$userId]);
        $stats = $result->fetch_assoc();
        
        $userData['total_quizzes'] = $stats['total_quizzes'];
        $userData['average_score'] = $stats['average_score'];
    }

} catch(Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $userData = [];
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updateData = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email']
        ];

        if ($userRole === 'teacher' && isset($_POST['department'])) {
            $updateData['department'] = $_POST['department'];
        }

        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                throw new Exception('Passwords do not match');
            }
            $updateData['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }

        updateUserProfile($userId, $updateData);
        $_SESSION['message'] = 'Profile updated successfully!';
        
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
    <!-- Reuse sidebar from student/teacher dashboard -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
        </div>
        <nav class="nav-links">
            <a href="<?= $userRole ?>_dashboard.php">
                <i class='bx bxs-dashboard'></i>
                <span>Dashboard</span>
            </a>
            <a href="quiz.php">
                <i class='bx bx-book-content'></i>
                <span>Quizzes</span>
            </a>
            <a href="profile.php" class="active">
                <i class='bx bx-user'></i>
                <span>Profile</span>
            </a>
            <a href="../actions/logout.php">
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <main class="profile-page">
        <div class="profile-header">
            <h1>My Profile</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
        </div>

        <div class="profile-content">
            <form action="profile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?= htmlspecialchars($userData['first_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?= htmlspecialchars($userData['last_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                </div>

                <?php if ($userRole === 'student'): ?>
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" 
                           value="<?= htmlspecialchars($userData['student_id'] ?? '') ?>" readonly>
                </div>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" 
                           value="<?= htmlspecialchars($userData['department'] ?? '') ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current)</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit" class="save-btn">Save Changes</button>
            </form>

            <?php if ($userRole === 'student'): ?>
            <div class="stats-section">
                <h2>Your Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class='bx bx-book-open'></i>
                        <div class="stat-info">
                            <h3>Total Quizzes</h3>
                            <p><?= $userData['total_quizzes'] ?? 0 ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-trophy'></i>
                        <div class="stat-info">
                            <h3>Average Score</h3>
                            <p><?= number_format($userData['average_score'] ?? 0, 1) ?>%</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/profile.js"></script>
</body>
</html>