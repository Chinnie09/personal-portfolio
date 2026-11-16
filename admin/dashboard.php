<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$pdo = getDbConnection();
$counts = [
    'skills' => (int) $pdo->query('SELECT COUNT(*) FROM skills')->fetchColumn(),
    'projects' => (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
    'messages' => (int) $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'unread' => (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn(),
];
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 18px; margin-bottom: 24px; }
        .card { background: #1f2937; border-radius: 12px; padding: 22px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .card h3 { margin-top: 0; color: #fbbf24; }
        .card p { font-size: 2rem; margin: 10px 0 0; }
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
                <input type="hidden" name="csrf_token" value="<?php echo e(generateAdminCsrfToken()); ?>">
                <button type="submit">Logout</button>
            </form>
        </aside>
        <main class="main">
            <h1>Welcome, <?php echo e($adminName); ?></h1>
            <div class="stats">
                <div class="card">
                    <h3>Total Skills</h3>
                    <p><?php echo (int) $counts['skills']; ?></p>
                </div>
                <div class="card">
                    <h3>Projects</h3>
                    <p><?php echo (int) $counts['projects']; ?></p>
                </div>
                <div class="card">
                    <h3>Messages</h3>
                    <p><?php echo (int) $counts['messages']; ?></p>
                </div>
                <div class="card">
                    <h3>Unread</h3>
                    <p><?php echo (int) $counts['unread']; ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
