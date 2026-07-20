<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['user']);
header('Location: login.php');
exit;
?>