<?php

require_once __DIR__ . '/logger.php';

// Start or resume the session if it is not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Returns true when a user is logged in.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

/**
 * Require the user to be logged in, otherwise redirect to login page.
 *
 * @return void
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        Logger::logUnauthorized('Attempt to access protected page without login');
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Check whether the current user has the required role.
 * Supports exact role or hierarchical role checking when needed.
 *
 * @param string $required_role
 * @return void
 */
function checkRole(string $required_role): void
{
    requireLogin();

    $user = $_SESSION['user'];
    $currentRole = $user['role'] ?? '';

    if ($currentRole !== $required_role) {
        Logger::logUnauthorized("Role mismatch: required $required_role, has $currentRole");
        http_response_code(403);
        echo 'Access denied. You do not have permission to view this page.';
        exit;
    }
}

/**
 * Helper to get the currently authenticated user data.
 *
 * @return array|null
 */
function currentUser(): ?array
{
    return isLoggedIn() ? $_SESSION['user'] : null;
}
