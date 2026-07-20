<?php

function getDbConnection(): PDO
{
    $host = '127.0.0.1';
    $dbName = 'miracle_notice_board';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        error_log('Database connection failed: ' . $e->getMessage());
        echo 'A database error occurred. Please try again later.';
        exit;
    }
}

return getDbConnection();
