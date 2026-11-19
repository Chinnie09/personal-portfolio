<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$pdo = getDbConnection();
$adminId = (int) $_SESSION['admin_id'];
$statement = $pdo->prepare('SELECT username, full_name, email, password_hash FROM admin_users WHERE id = :id');
$statement->execute([':id' => $adminId]);
$admin = $statement->fetch();

if (!$admin) {
    destroyAdminSession();
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid form request.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'profile') {
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));

            if ($fullName === '' || strlen($fullName) > 120 || !isValidEmail($email)) {
                $error = 'Enter a name and a valid email address.';
            } else {
                $update = $pdo->prepare('UPDATE admin_users SET full_name = :full_name, email = :email WHERE id = :id');
                $update->execute([
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':id' => $adminId,
                ]);
                $_SESSION['admin_name'] = $fullName;
                $message = 'Admin profile updated.';
            }
        } elseif ($action === 'password') {
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if (!password_verify($currentPassword, $admin['password_hash'])) {
                $error = 'The current password is incorrect.';
            } elseif (strlen($newPassword) < 12) {
                $error = 'The new password must be at least 12 characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'The new passwords do not match.';
            } else {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $pdo->prepare('UPDATE admin_users SET password_hash = :password_hash WHERE id = :id');
                $update->execute([
                    ':password_hash' => $newHash,
                    ':id' => $adminId,
                ]);
                $admin['password_hash'] = $newHash;
                $message = 'Admin password updated.';
            }
        }

        if ($message !== '') {
            $statement->execute([':id' => $adminId]);
            $admin = $statement->fetch();
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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { max-width: 720px; background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 24px; }
        input { box-sizing: border-box; width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: white; margin: 8px 0 16px; }
        button { padding: 10px 14px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <h2>Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Admin Profile</a>
        <a href="settings.php">Website Settings</a>
        <a href="skills.php">Skills</a>
        <a href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <form method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
            <button type="submit">Logout</button>
        </form>
    </aside>
    <main class="main">
        <h1>Admin Profile</h1>
        <p>Username: <?php echo e($admin['username']); ?></p>
        <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
        <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
        <section class="panel">
            <h2>Update Name and Email</h2>
            <form method="post" action="profile.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <input type="hidden" name="action" value="profile">
                <label for="full_name">Full Name</label>
                <input id="full_name" name="full_name" maxlength="120" required value="<?php echo e($admin['full_name']); ?>">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" maxlength="254" required value="<?php echo e($admin['email']); ?>">
                <button type="submit">Save Profile</button>
            </form>
        </section>
        <section class="panel">
            <h2>Change Password</h2>
            <form method="post" action="profile.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <input type="hidden" name="action" value="password">
                <label for="current_password">Current Password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                <label for="new_password">New Password (at least 12 characters)</label>
                <input id="new_password" name="new_password" type="password" minlength="12" autocomplete="new-password" required>
                <label for="confirm_password">Confirm New Password</label>
                <input id="confirm_password" name="confirm_password" type="password" minlength="12" autocomplete="new-password" required>
                <button type="submit">Change Password</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
