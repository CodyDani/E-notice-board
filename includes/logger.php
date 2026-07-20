<?php
declare(strict_types=1);

/**
 * Logger utility for error logging and debugging.
 */
class Logger
{
    private static string $logFile = __DIR__ . '/../logs/error.log';

    /**
     * Log a message to the error log file.
     *
     * @param string $message
     * @param string $level (e.g., 'INFO', 'ERROR', 'WARNING')
     * @return void
     */
    public static function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';

        $logEntry = sprintf(
            "[%s] [%s] IP: %s | URI: %s | UA: %s | %s\n",
            $timestamp,
            $level,
            $ip,
            $requestUri,
            $userAgent,
            $message
        );

        // Ensure log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log unauthorized access attempt.
     *
     * @param string $reason
     * @return void
     */
    public static function logUnauthorized(string $reason): void
    {
        $userId = $_SESSION['user']['id'] ?? 'guest';
        $userRole = $_SESSION['user']['role'] ?? 'none';
        self::log("Unauthorized access: $reason | User ID: $userId | Role: $userRole", 'WARNING');
    }

    /**
     * Log general error.
     *
     * @param string $error
     * @return void
     */
    public static function logError(string $error): void
    {
        self::log("Error: $error", 'ERROR');
    }
}