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
        $title = trim((string) ($_POST['title'] ?? ''));
        $summary = trim((string) ($_POST['summary'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

        if ($action === 'delete' && $id > 0) {
            $pdo->prepare('DELETE FROM projects WHERE id = :id')->execute([':id' => $id]);
            $message = 'Project deleted successfully.';
        } elseif ($action === 'save') {
            if ($title === '' || $summary === '' || $description === '') {
                $error = 'Please complete all required project fields.';
            } else {
                $imageValue = $imageUrl !== '' ? $imageUrl : 'images/project1.jpg';
                if (!empty($_FILES['project_image']['name'])) {
                    $file = $_FILES['project_image'];
                    $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif'];

                    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                        $error = 'Image upload failed. Please choose a JPG, PNG, or GIF image.';
                    } elseif ($file['size'] > 2 * 1024 * 1024) {
                        $error = 'Image upload failed. The image must be 2MB or smaller.';
                    } else {
                        $imageInfo = getimagesize($file['tmp_name']);
                        $mime = $imageInfo === false ? '' : $imageInfo['mime'];

                        if ($imageInfo === false || !isset($allowed[$mime])) {
                            $error = 'Image upload failed. The file is not a supported image.';
                        } else {
                            $targetDir = __DIR__ . '/../uploads';
                            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                                throw new RuntimeException('Unable to create the upload directory.');
                            }

                            $fileName = 'project_' . bin2hex(random_bytes(16)) . $allowed[$mime];
                            if (move_uploaded_file($file['tmp_name'], $targetDir . '/' . $fileName)) {
                                $imageValue = 'uploads/' . $fileName;
                            } else {
                                $error = 'The project image could not be saved.';
                            }
                        }
                    }
                }

                if ($error === '' && $id > 0) {
                    $stmt = $pdo->prepare('UPDATE projects SET title = :title, summary = :summary, description = :description, image_url = :image_url WHERE id = :id');
                    $stmt->execute([':title' => $title, ':summary' => $summary, ':description' => $description, ':image_url' => $imageValue, ':id' => $id]);
                    $message = 'Project updated successfully.';
                } elseif ($error === '') {
                    $stmt = $pdo->prepare('INSERT INTO projects (title, summary, description, image_url) VALUES (:title, :summary, :description, :image_url)');
                    $stmt->execute([':title' => $title, ':summary' => $summary, ':description' => $description, ':image_url' => $imageValue]);
                    $message = 'Project added successfully.';
                }
            }
        }
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order, id')->fetchAll();
$editingProject = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $editStatement = $pdo->prepare('SELECT * FROM projects WHERE id = :id');
    $editStatement->execute([':id' => $editId]);
    $editingProject = $editStatement->fetch() ?: null;
}
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 24px; }
        input, textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: white; margin-top: 8px; }
        button { padding: 10px 14px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #374151; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <h2>Admin</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Admin Profile</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="education.php">Education</a>
            <a href="experience.php">Experience</a>
            <a href="achievements.php">Achievements</a>
            <a href="messages.php">Messages</a>
            <a href="settings.php">Settings</a>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                <button type="submit">Logout</button>
            </form>
        </aside>
        <main class="main">
            <h1>Manage Projects</h1>
            <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
            <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
            <div class="panel">
                <h2><?php echo $editingProject ? 'Edit Project' : 'Add Project'; ?></h2>
                <form method="post" action="projects.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int) ($editingProject['id'] ?? 0); ?>">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo e($editingProject['title'] ?? ''); ?>" required>
                    <label>Summary</label>
                    <textarea name="summary" rows="3" required><?php echo e($editingProject['summary'] ?? ''); ?></textarea>
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo e($editingProject['description'] ?? ''); ?></textarea>
                    <label>Image URL</label>
                    <input type="text" name="image_url" placeholder="images/project1.jpg" value="<?php echo e($editingProject['image_url'] ?? ''); ?>">
                    <label>Or upload a project image (JPG, PNG, or GIF; max 2MB)</label>
                    <input type="file" name="project_image" accept="image/jpeg,image/png,image/gif">
                    <p style="margin-top:10px;"><button type="submit"><?php echo $editingProject ? 'Update Project' : 'Add Project'; ?></button></p>
                </form>
            </div>
            <div class="panel">
                <h2>Current Projects</h2>
                <table>
                    <thead>
                        <tr><th>Title</th><th>Summary</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo e($project['title']); ?></td>
                                <td><?php echo e($project['summary']); ?></td>
                                <td>
                                    <a href="projects.php?edit=<?php echo (int) $project['id']; ?>">Edit</a>
                                    <form method="post" action="projects.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $project['id']; ?>">
                                        <button type="submit" onclick="return confirm('Delete this project?')">Delete</button>
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
