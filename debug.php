<?php
declare(strict_types=1);

require_once 'includes/auth.php';
checkRole('admin'); // Only admins can view logs

$logFile = __DIR__ . '/logs/error.log';

if (!file_exists($logFile)) {
    echo '<p>No logs available.</p>';
    exit;
}

$logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$logs = array_reverse($logs); // Most recent first

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Logs - Miracle's Notice Board</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Debug Logs</h1>
            <nav>
                <a href="admin/dashboard.php">Back to Dashboard</a>
            </nav>
        </header>
        <main>
            <h2>Error and Access Logs</h2>
            <pre><?php foreach ($logs as $log) { echo htmlspecialchars($log) . "\n"; } ?></pre>
        </main>
    </div>
</body>
</html>