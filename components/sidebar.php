<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['role'];
?>

<style>
    /* Sidebar specific styles */
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: var(--color-light);
        position: fixed;
        left: 0;
        top: 0;
        padding: 1rem;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .logo-container {
        padding: 1rem;
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-container img {
        width: 120px;
        height: auto;
    }

    .nav-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .nav-links a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        color: var(--color-dark);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .nav-links a:hover,
    .nav-links a.active {
        background-color: var(--color-primary);
        color: var(--color-light);
    }

    .nav-links i {
        font-size: 1.5rem;
    }

    /* Mobile responsive styles */
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
        }
        
        .sidebar span {
            display: none;
        }
        
        .logo-container img {
            width: 40px;
        }
    }
</style>

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