<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}
echo 'Sidebar loaded!';

$userRole = $_SESSION['role'];
?>

<div class="sidebar">
    <div class="logo-container">
        <img src="../assets/images/KUMI_logo.svg" alt="Kumi Logo">
    </div>
    <nav class="nav-links">
        <a href="<?= $userRole ?>_dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class='bx bxs-dashboard'></i>
            <span>Dashboard</span>
        </a>
        <a href="quiz.php" class="<?= $currentPage === 'quiz' ? 'active' : '' ?>">
            <i class='bx bx-book-content'></i>
            <span>Quizzes</span>
        </a>
        <a href="profile.php" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
            <i class='bx bx-user'></i>
            <span>Profile</span>
        </a>
        <a href="../actions/logout.php">
            <i class='bx bx-log-out'></i>
            <span>Logout</span>
        </a>
    </nav>
</div> 