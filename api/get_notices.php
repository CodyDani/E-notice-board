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

require_once __DIR__ . '/../config/db.php';

try {
    $db = getDbConnection();
    $stmt = $db->prepare(
        'SELECT id, title, content, category, target_role, created_at
         FROM notices
         WHERE target_role = :role OR target_role = :all
         ORDER BY created_at DESC'
    );
    $stmt->execute([
        'role' => $role,
        'all' => 'all',
    ]);

    $notices = $stmt->fetchAll();

    echo json_encode($notices ?: []);
} catch (PDOException $exception) {
    Logger::logError('Unable to fetch notices: ' . $exception->getMessage());
    http_response_code(500);
    error_log('Unable to fetch notices: ' . $exception->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unable to retrieve notices.']);
}
