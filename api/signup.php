<?php
// declare(strict_types=1);

require_once __DIR__ . '/../includes/logger.php';

header('Content-Type: application/json; charset=utf-8');

$body = file_get_contents('php://input');
if ($body === false) {
    $body = '';
}

$data = null;
if ($body !== '' && trim($body) !== '') {
    $decoded = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $data = $decoded;
    }
}

if ($data === null && !empty($_POST)) {
    $data = $_POST;
}

if ($data === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request body is required.']);
    exit;
}

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
    exit;
}

$fullName = trim((string) ($data['full_name'] ?? ''));
$role = trim((string) ($data['role'] ?? ''));
$identifier = trim((string) ($data['identifier'] ?? ''));
$password = (string) ($data['password'] ?? '');

if ($fullName === '' || $role === '' || $identifier === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name, role, identifier, and password are all required.']);
    exit;
}

$allowedRoles = ['student', 'lecturer'];
if (!in_array($role, $allowedRoles, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role. Only student or lecturer registration is allowed.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $db = getDbConnection();

    $stmt = $db->prepare('SELECT id FROM users WHERE identifier = ? LIMIT 1');
    $stmt->execute([$identifier]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'The provided identifier is already registered.']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        Logger::logError('Password hashing failed during signup.');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Unable to process registration.']);
        exit;
    }

    $safeIdentifier = strtolower(preg_replace('/[^a-z0-9._-]+/', '.', $identifier));
    $safeIdentifier = trim($safeIdentifier, '.');
    $email = sprintf('%s+%s@miraclenotice.local', $role, $safeIdentifier);

    $insert = $db->prepare('INSERT INTO users (full_name, email, password_hash, role, identifier) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([$fullName, $email, $passwordHash, $role, $identifier]);

    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} catch (PDOException $exception) {
    Logger::logError('Signup failure: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
}
