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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid session request. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
        } else {
            $statement = $pdo->prepare('SELECT id, username, password_hash, full_name FROM admin_users WHERE username = :username LIMIT 1');
            $statement->execute([':username' => $username]);
            $admin = $statement->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int) $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                header('Location: dashboard.php');
                exit;
            }

            $error = 'Invalid login credentials. Please try again.';
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
