<?php
declare(strict_types=1);

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $started = session_start([
        'use_strict_mode' => true,
        'use_only_cookies' => true,
        'cookie_httponly' => true,
        'cookie_secure' => $isHttps,
        'cookie_samesite' => 'Lax',
    ]);

    if (!$started) {
        throw new RuntimeException('Unable to start the session required for CSRF protection.');
    }
}

function generateCsrfToken(): string
{
    startSecureSession();

    if (
        !isset($_SESSION['csrf_token'])
        || !is_string($_SESSION['csrf_token'])
        || !preg_match('/\A[a-f0-9]{64}\z/', $_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(mixed $token): bool
{
    startSecureSession();

    $expectedToken = $_SESSION['csrf_token'] ?? null;

    return is_string($token)
        && $token !== ''
        && is_string($expectedToken)
        && hash_equals($expectedToken, $token);
}
