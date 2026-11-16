<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$pdo = getDbConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid request.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete' && $id > 0) {
            $pdo->prepare('DELETE FROM messages WHERE id = :id')->execute([':id' => $id]);
            $message = 'Message deleted.';
        } elseif ($action === 'toggle' && $id > 0) {
            $stmt = $pdo->prepare('UPDATE messages SET status = CASE WHEN status = "unread" THEN "read" ELSE "unread" END WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $message = 'Message status updated.';
        }
    }
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #374151; padding: 10px; text-align: left; vertical-align: top; }
        button { padding: 9px 12px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .status { font-weight: 700; }
        .success { color: #4ade80; }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <h2>Admin</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php">Messages</a>
            <a href="settings.php">Settings</a>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <button type="submit">Logout</button>
            </form>
        </aside>
        <main class="main">
            <h1>Messages</h1>
            <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
            <div class="panel">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $entry): ?>
                            <tr>
                                <td><?php echo e($entry['name']); ?></td>
                                <td><?php echo e($entry['email']); ?></td>
                                <td><?php echo e($entry['subject']); ?></td>
                                <td><?php echo e($entry['message']); ?></td>
                                <td class="status"><?php echo e($entry['status']); ?></td>
                                <td>
                                    <form method="post" action="messages.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?php echo (int) $entry['id']; ?>">
                                        <button type="submit"><?php echo $entry['status'] === 'unread' ? 'Mark Read' : 'Mark Unread'; ?></button>
                                    </form>
                                    <form method="post" action="messages.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $entry['id']; ?>">
                                        <button type="submit" onclick="return confirm('Delete this message?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
