<?php
require_once __DIR__ . '/../includes/auth.php';

startAppSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Invalid logout request.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: login.php');
exit;
