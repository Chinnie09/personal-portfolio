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
            $pdo->prepare('DELETE FROM education WHERE id = :id')->execute([':id' => $id]);
            $message = 'Education record deleted.';
        } elseif ($action === 'save') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $institution = trim((string) ($_POST['institution'] ?? ''));
            $period = trim((string) ($_POST['period'] ?? ''));
            $details = trim((string) ($_POST['details'] ?? ''));
            $sortOrder = max(0, (int) ($_POST['sort_order'] ?? 0));

            if ($title === '' || $institution === '' || $period === '' || $details === '') {
                $error = 'Please complete all education fields.';
            } elseif ($id > 0) {
                $statement = $pdo->prepare(
                    'UPDATE education SET title = :title, institution = :institution, period = :period, details = :details, sort_order = :sort_order WHERE id = :id'
                );
                $statement->execute([
                    ':title' => $title,
                    ':institution' => $institution,
                    ':period' => $period,
                    ':details' => $details,
                    ':sort_order' => $sortOrder,
                    ':id' => $id,
                ]);
                $message = 'Education record updated.';
            } else {
                $statement = $pdo->prepare(
                    'INSERT INTO education (title, institution, period, details, sort_order) VALUES (:title, :institution, :period, :details, :sort_order)'
                );
                $statement->execute([
                    ':title' => $title,
                    ':institution' => $institution,
                    ':period' => $period,
                    ':details' => $details,
                    ':sort_order' => $sortOrder,
                ]);
                $message = 'Education record added.';
            }
        }
    }
}

$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $statement = $pdo->prepare('SELECT * FROM education WHERE id = :id');
    $statement->execute([':id' => $editId]);
    $editing = $statement->fetch() ?: null;
}
$records = $pdo->query('SELECT * FROM education ORDER BY sort_order, id')->fetchAll();
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education</title>
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
        <h1>Manage Education</h1>
        <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
        <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
        <div class="panel">
            <h2><?php echo $editing ? 'Edit Education' : 'Add Education'; ?></h2>
            <form method="post" action="education.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?php echo (int) ($editing['id'] ?? 0); ?>">
                <label for="title">Program or qualification</label>
                <input id="title" name="title" maxlength="180" required value="<?php echo e($editing['title'] ?? ''); ?>">
                <label for="institution">School or institution</label>
                <input id="institution" name="institution" maxlength="180" required value="<?php echo e($editing['institution'] ?? ''); ?>">
                <label for="period">Period</label>
                <input id="period" name="period" maxlength="80" required value="<?php echo e($editing['period'] ?? ''); ?>">
                <label for="details">Details</label>
                <textarea id="details" name="details" rows="4" required><?php echo e($editing['details'] ?? ''); ?></textarea>
                <label for="sort_order">Display order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="<?php echo (int) ($editing['sort_order'] ?? 0); ?>">
                <button type="submit"><?php echo $editing ? 'Save Changes' : 'Add Education'; ?></button>
            </form>
        </div>
        <div class="panel">
            <h2>Education Records</h2>
            <table>
                <thead><tr><th>Qualification</th><th>Institution</th><th>Period</th><th>Details</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo e($record['title']); ?></td>
                        <td><?php echo e($record['institution']); ?></td>
                        <td><?php echo e($record['period']); ?></td>
                        <td><?php echo e($record['details']); ?></td>
                        <td>
                            <a href="education.php?edit=<?php echo (int) $record['id']; ?>">Edit</a>
                            <form method="post" action="education.php" style="display:inline;">
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
