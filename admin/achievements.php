<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$pdo = getDbConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid form request.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete' && $id > 0) {
            $pdo->prepare('DELETE FROM achievements WHERE id = :id')->execute([':id' => $id]);
            $message = 'Achievement deleted.';
        } elseif ($action === 'save') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $issuer = trim((string) ($_POST['issuer'] ?? ''));
            $issueDate = trim((string) ($_POST['issue_date'] ?? ''));
            $reference = trim((string) ($_POST['reference'] ?? ''));
            $sortOrder = max(0, (int) ($_POST['sort_order'] ?? 0));

            if ($title === '' || $issuer === '' || $issueDate === '' || $reference === '') {
                $error = 'Please complete all achievement fields.';
            } elseif ($id > 0) {
                $statement = $pdo->prepare(
                    'UPDATE achievements SET title = :title, issuer = :issuer, issue_date = :issue_date, reference = :reference, sort_order = :sort_order WHERE id = :id'
                );
                $statement->execute([
                    ':title' => $title,
                    ':issuer' => $issuer,
                    ':issue_date' => $issueDate,
                    ':reference' => $reference,
                    ':sort_order' => $sortOrder,
                    ':id' => $id,
                ]);
                $message = 'Achievement updated.';
            } else {
                $statement = $pdo->prepare(
                    'INSERT INTO achievements (title, issuer, issue_date, reference, sort_order) VALUES (:title, :issuer, :issue_date, :reference, :sort_order)'
                );
                $statement->execute([
                    ':title' => $title,
                    ':issuer' => $issuer,
                    ':issue_date' => $issueDate,
                    ':reference' => $reference,
                    ':sort_order' => $sortOrder,
                ]);
                $message = 'Achievement added.';
            }
        }
    }
}

$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $statement = $pdo->prepare('SELECT * FROM achievements WHERE id = :id');
    $statement->execute([':id' => $editId]);
    $editing = $statement->fetch() ?: null;
}
$records = $pdo->query('SELECT * FROM achievements ORDER BY sort_order, id')->fetchAll();
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 24px; overflow-x: auto; }
        input, textarea { box-sizing: border-box; width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: white; margin: 8px 0 14px; }
        button { padding: 10px 14px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #374151; padding: 10px; text-align: left; vertical-align: top; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <h2>Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="settings.php">Profile &amp; Settings</a>
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
        <h1>Manage Achievements</h1>
        <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
        <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
        <div class="panel">
            <h2><?php echo $editing ? 'Edit Achievement' : 'Add Achievement'; ?></h2>
            <form method="post" action="achievements.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
                <label for="title">Certificate or award</label>
                <input id="title" name="title" maxlength="180" required value="<?php echo e($editing['title'] ?? ''); ?>">
                <label for="issuer">Issuer</label>
                <input id="issuer" name="issuer" maxlength="180" required value="<?php echo e($editing['issuer'] ?? ''); ?>">
                <label for="issue_date">Date or completion period</label>
                <input id="issue_date" name="issue_date" maxlength="80" required value="<?php echo e($editing['issue_date'] ?? ''); ?>">
                <label for="reference">Certificate ID or notes</label>
                <input id="reference" name="reference" maxlength="200" required value="<?php echo e($editing['reference'] ?? ''); ?>">
                <label for="sort_order">Display order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="<?php echo (int) ($editing['sort_order'] ?? 0); ?>">
                <button type="submit"><?php echo $editing ? 'Save Changes' : 'Add Achievement'; ?></button>
            </form>
        </div>
        <div class="panel">
            <h2>Achievements</h2>
            <table>
                <thead><tr><th>Name</th><th>Issuer</th><th>Date</th><th>Reference</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo e($record['title']); ?></td>
                        <td><?php echo e($record['issuer']); ?></td>
                        <td><?php echo e($record['issue_date']); ?></td>
                        <td><?php echo e($record['reference']); ?></td>
                        <td>
                            <a href="achievements.php?edit=<?php echo (int) $record['id']; ?>">Edit</a>
                            <form method="post" action="achievements.php" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int) $record['id']; ?>">
                                <button type="submit">Delete</button>
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
