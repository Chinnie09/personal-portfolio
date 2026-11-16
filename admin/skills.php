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
        $category = trim((string) ($_POST['category'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($action === 'delete' && $id > 0) {
            $pdo->prepare('DELETE FROM skills WHERE id = :id')->execute([':id' => $id]);
            $message = 'Skill deleted successfully.';
        } elseif ($action === 'save') {
            if ($category === '' || $name === '' || $description === '') {
                $error = 'Please complete all skill fields.';
            } else {
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE skills SET category = :category, name = :name, description = :description WHERE id = :id');
                    $stmt->execute([':category' => $category, ':name' => $name, ':description' => $description, ':id' => $id]);
                    $message = 'Skill updated successfully.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO skills (category, name, description) VALUES (:category, :name, :description)');
                    $stmt->execute([':category' => $category, ':name' => $name, ':description' => $description]);
                    $message = 'Skill added successfully.';
                }
            }
        }
    }
}

$skills = $pdo->query('SELECT * FROM skills ORDER BY category, sort_order, id')->fetchAll();
$editingSkill = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $editStatement = $pdo->prepare('SELECT * FROM skills WHERE id = :id');
    $editStatement->execute([':id' => $editId]);
    $editingSkill = $editStatement->fetch() ?: null;
}
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #374151; padding: 10px; text-align: left; }
        input, textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: white; margin-top: 8px; }
        button { padding: 10px 14px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .muted { color: #9ca3af; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
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
            <h1>Manage Skills</h1>
            <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
            <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
            <div class="panel">
                <h2>Add/Edit Skill</h2>
                <form method="post" action="skills.php">
                    <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int) ($editingSkill['id'] ?? 0); ?>">
                    <label>Category</label>
                    <input type="text" name="category" placeholder="Programming & Database" value="<?php echo e($editingSkill['category'] ?? ''); ?>" required>
                    <label>Name</label>
                    <input type="text" name="name" placeholder="PHP" value="<?php echo e($editingSkill['name'] ?? ''); ?>" required>
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the skill..." required><?php echo e($editingSkill['description'] ?? ''); ?></textarea>
                    <p style="margin-top: 10px;"><button type="submit"><?php echo $editingSkill ? 'Update Skill' : 'Add Skill'; ?></button></p>
                </form>
            </div>

            <div class="panel">
                <h2>Current Skills</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td><?php echo e($skill['category']); ?></td>
                                <td><?php echo e($skill['name']); ?></td>
                                <td><?php echo e($skill['description']); ?></td>
                                <td>
                                    <a href="skills.php?edit=<?php echo (int) $skill['id']; ?>">Edit</a>
                                    <form method="post" action="skills.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $skill['id']; ?>">
                                        <button type="submit" onclick="return confirm('Delete this skill?')">Delete</button>
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
