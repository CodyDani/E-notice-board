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
if ($role !== 'admin') {
    Logger::logUnauthorized("Role not allowed for notice deletion: $role");
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden. Only admins can delete notices.']);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
        exit;
    }
} else {
    $data = $_POST;
}

if (!is_array($data) || empty($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Request data is required.']);
    exit;
}

$noticeId = $data['notice_id'] ?? '';
if (!is_numeric($noticeId) || (int)$noticeId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid notice_id is required.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $db = getDbConnection();
    $stmt = $db->prepare('DELETE FROM notices WHERE id = ?');
    $stmt->execute([(int)$noticeId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Notice deleted successfully.',
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Notice not found.']);
    }
} catch (PDOException $exception) {
    Logger::logError('Delete notice failed: ' . $exception->getMessage());
    http_response_code(500);
    error_log('Delete notice failed: ' . $exception->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unable to delete notice.']);
}
