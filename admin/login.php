<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

startAppSession();

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$pdo = getDbConnection();
$error = '';
$maxLoginAttempts = 5;
$lockoutSeconds = 15 * 60;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid session request. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
        } else {
            $attemptKey = hash(
                'sha256',
                strtolower($username) . '|' . ($_SERVER['REMOTE_ADDR'] ?? '')
            );
            $now = time();

            $cleanup = $pdo->prepare('DELETE FROM admin_login_attempts WHERE last_attempt < :oldest_attempt');
            $cleanup->execute([':oldest_attempt' => $now - 86400]);

            $attemptQuery = $pdo->prepare(
                'SELECT failed_attempts, last_attempt FROM admin_login_attempts WHERE attempt_key = :attempt_key'
            );
            $attemptQuery->execute([':attempt_key' => $attemptKey]);
            $loginAttempt = $attemptQuery->fetch();

            $recentAttempts = $loginAttempt
                && $now - (int) $loginAttempt['last_attempt'] < $lockoutSeconds;

            if ($recentAttempts && (int) $loginAttempt['failed_attempts'] >= $maxLoginAttempts) {
                $error = 'Too many failed attempts. Please wait 15 minutes before trying again.';
            } else {
                $statement = $pdo->prepare('SELECT id, username, password_hash, full_name FROM admin_users WHERE username = :username LIMIT 1');
                $statement->execute([':username' => $username]);
                $admin = $statement->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
                        $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
                        $updatePassword = $pdo->prepare(
                            'UPDATE admin_users SET password_hash = :password_hash WHERE id = :id'
                        );
                        $updatePassword->execute([
                            ':password_hash' => $newPasswordHash,
                            ':id' => $admin['id'],
                        ]);
                    }

                    $clearAttempts = $pdo->prepare('DELETE FROM admin_login_attempts WHERE attempt_key = :attempt_key');
                    $clearAttempts->execute([':attempt_key' => $attemptKey]);

                    session_regenerate_id(true);
                    unset($_SESSION['csrf_token']);
                    $_SESSION['admin_id'] = (int) $admin['id'];
                    $_SESSION['admin_name'] = $admin['full_name'];
                    $_SESSION['last_activity'] = time();
                    header('Location: dashboard.php');
                    exit;
                }

                $failedAttempts = $recentAttempts
                    ? (int) $loginAttempt['failed_attempts'] + 1
                    : 1;
                $saveAttempt = $pdo->prepare(
                    'INSERT INTO admin_login_attempts (attempt_key, failed_attempts, last_attempt)
                    VALUES (:attempt_key, :failed_attempts, :last_attempt)
                    ON DUPLICATE KEY UPDATE failed_attempts = :updated_attempts, last_attempt = :updated_time'
                );
                $saveAttempt->execute([
                    ':attempt_key' => $attemptKey,
                    ':failed_attempts' => $failedAttempts,
                    ':last_attempt' => $now,
                    ':updated_attempts' => $failedAttempts,
                    ':updated_time' => $now,
                ]);
                $error = 'Invalid login credentials. Please try again.';
            }
        }
    }
}

$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #111827; color: #f3f4f6; font-family: Arial, sans-serif; }
        .login-container { max-width: 440px; margin: 80px auto; background: #1f2937; padding: 30px; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.25); }
        .login-container h1 { text-align: center; margin-bottom: 20px; }
        .login-container label { display: block; margin-bottom: 8px; font-weight: 600; }
        .login-container input { width: 100%; padding: 12px 14px; margin-bottom: 18px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: #fff; }
        .login-container button { width: 100%; padding: 12px; border: 0; border-radius: 8px; background: #f4b400; color: #111; font-weight: 700; cursor: pointer; }
        .error { color: #f87171; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Portfolio Admin</h1>
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Enter password" required>

            <button type="submit">Login</button>

            <?php if ($error !== ''): ?>
                <p class="error"><?php echo e($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
