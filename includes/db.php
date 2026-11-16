<?php
declare(strict_types=1);

function getDbConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $config = [];
    $localConfigPath = __DIR__ . '/config.local.php';

    if (is_file($localConfigPath)) {
        $localConfig = require $localConfigPath;
        if (!is_array($localConfig)) {
            throw new RuntimeException('Database configuration must return an array.');
        }
        $config = $localConfig;
    }

    $dsnFromEnvironment = getenv('DB_DSN');
    $dsn = $dsnFromEnvironment !== false ? $dsnFromEnvironment : ($config['dsn'] ?? null);
    $userFromEnvironment = getenv('DB_USER');
    $username = $userFromEnvironment !== false ? $userFromEnvironment : ($config['username'] ?? '');
    $passwordFromEnvironment = getenv('DB_PASSWORD');
    $password = $passwordFromEnvironment !== false ? $passwordFromEnvironment : ($config['password'] ?? '');

    if (!is_string($dsn) || trim($dsn) === '') {
        throw new RuntimeException('Database DSN is missing. Copy includes/config.example.php to includes/config.local.php and configure it.');
    }
    if (!is_string($username) || !is_string($password)) {
        throw new RuntimeException('Database username and password must be strings.');
    }

    $connection = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}
