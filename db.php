<?php

$dbConfig = [
    'host' => '127.0.0.1',
    'name' => 'student_registration_system',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
];

function createServerConnection(): PDO
{
    global $dbConfig;

    $dsn = "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

function createDatabaseConnection(): PDO
{
    global $dbConfig;

    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

function initializeDatabase(): void
{
    global $dbConfig;

    $pdo = createServerConnection();
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['name']}` CHARACTER SET {$dbConfig['charset']} COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbConfig['name']}`");
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS Students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            program VARCHAR(100) NOT NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );
}
