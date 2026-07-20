<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/logger.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (!is_array($user) || empty($user['role'])) {
    Logger::logUnauthorized('API access without valid session');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

$role = (string) $user['role'];
if ($role !== 'admin' && $role !== 'lecturer') {
    Logger::logUnauthorized("Role not allowed for notice creation: $role");
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden. You do not have permission to post notices.']);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    // Expect JSON payload
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
        exit;
    }
} else {
    // Assume form data
    $data = $_POST;
}

if (!is_array($data) || empty($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request data is required.']);
    exit;
}

$title = trim((string) ($data['title'] ?? ''));
$contentText = trim((string) ($data['content'] ?? ''));
$category = trim((string) ($data['category'] ?? ''));
$target_role = trim((string) ($data['target_role'] ?? ''));

$allowedRoles = ['student', 'lecturer', 'admin', 'all'];

if ($title === '' || $contentText === '' || $category === '' || $target_role === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Title, content, category, and target_role are required.']);
    exit;
}

if (!in_array($target_role, $allowedRoles, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid target_role. Allowed values are student, lecturer, admin, or all.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $db = getDbConnection();
    $stmt = $db->prepare(
        'INSERT INTO notices (title, content, category, target_role) VALUES (:title, :content, :category, :target_role)'
    );
    $stmt->execute([
        'title' => $title,
        'content' => $contentText,
        'category' => $category,
        'target_role' => $target_role,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Notice created successfully.',
        'notice_id' => (int) $db->lastInsertId(),
    ]);
} catch (PDOException $exception) {
    Logger::logError('Create notice failed: ' . $exception->getMessage());
    http_response_code(500);
    error_log('Create notice failed: ' . $exception->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unable to create notice.']);
}
