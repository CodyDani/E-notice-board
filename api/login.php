<?php
// declare(strict_types=1);

require_once __DIR__ . '/../includes/logger.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$identifier = trim((string) ($data['identifier'] ?? ''));
$password = trim((string) ($data['password'] ?? ''));
$role = trim((string) ($data['role'] ?? ''));

// Debug: Log what we received
error_log('Login attempt - Received data: ' . json_encode($data));
error_log('Identifier value: "' . $identifier . '" (length: ' . strlen($identifier) . ')');
error_log('Role value: "' . $role . '"');

if ($identifier === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Identifier (matric number/staff ID) is required.']);
    exit;
}

if ($password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password is required.']);
    exit;
}

if ($role === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Role is required.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = getDbConnection()->prepare('SELECT id, full_name, password_hash, role FROM users WHERE identifier = ? LIMIT 1');
    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    if (!$user) {
        Logger::logUnauthorized("Failed login attempt for identifier: $identifier");
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        exit;
    }

    if ($user['role'] !== $role) {
        Logger::logUnauthorized("Failed login attempt for identifier: $identifier - role mismatch (expected: $role, actual: {$user['role']})");
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        exit;
    }

    if (!password_verify($password, $user['password_hash'])) {
        Logger::logUnauthorized("Failed password verification for identifier: $identifier");
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        exit;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
    ];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful.',
        'role' => $user['role'],
    ]);
} catch (PDOException $exception) {
    Logger::logError('Login failure: ' . $exception->getMessage());
    http_response_code(500);
    error_log('Login failure: ' . $exception->getMessage());
    echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
}

