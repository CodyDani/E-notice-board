<?php
// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];

    // Route user based on their role
    switch ($role) {
        case 'admin':
            header('Location: admin/dashboard.php');
            exit;
        case 'lecturer':
            header('Location: lecturer/dashboard.php');
            exit;
        case 'student':
            header('Location: student/dashboard.php');
            exit;
        default:
            // Fallback for invalid roles
            header('Location: login.php');
            exit;
    }
} else {
    // If no session exists, redirect to the login page
    header('Location: login.php');
    exit;
}
?>