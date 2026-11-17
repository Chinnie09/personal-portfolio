<?php
declare(strict_types=1);

require_once __DIR__ . '/security.php';

function startAppSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        startSecureSession();
    }
}

function isAdminLoggedIn(): bool
{
    startAppSession();

    if (
        !isset($_SESSION['admin_id'])
        || !is_numeric($_SESSION['admin_id'])
        || (int) $_SESSION['admin_id'] <= 0
    ) {
        return false;
    }

    $lastActivity = (int) ($_SESSION['last_activity'] ?? 0);
    if ($lastActivity === 0 || time() - $lastActivity > 1800) {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['last_activity']);
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}

function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function generateAdminCsrfToken(): string
{
    startAppSession();

    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyAdminCsrfToken(mixed $token): bool
{
    startAppSession();

    if (!is_string($token) || $token === '') {
        return false;
    }

    return isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
