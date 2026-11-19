<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$pdo = getDbConnection();
$settings = getSiteSettings($pdo);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyAdminCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid form request.';
    } else {
        $defaultTheme = $_POST['default_theme'] ?? '';
        if (!in_array($defaultTheme, ['dark', 'light'], true)) {
            $error = 'Please choose a valid default theme.';
        }

        $imageSettings = [];
        if ($error === '') {
            foreach (['profile_image' => 'profile', 'logo_image' => 'logo'] as $field => $prefix) {
                if (!empty($_FILES[$field]['name'])) {
                    try {
                        $imageSettings[$field] = savePortfolioImage($_FILES[$field], $prefix);
                    } catch (RuntimeException $exception) {
                        $error = $exception->getMessage();
                        break;
                    }
                }
            }
        }

        if ($error === '') {
            $fields = ['name', 'title', 'intro', 'tagline', 'bio', 'email', 'location', 'facebook', 'instagram'];
            foreach ($fields as $field) {
                $value = trim((string) ($_POST[$field] ?? ''));
                $statement = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :insert_value) ON DUPLICATE KEY UPDATE setting_value = :update_value');
                $statement->execute([':key' => $field, ':insert_value' => $value, ':update_value' => $value]);
            }

            $themeStatement = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :insert_value) ON DUPLICATE KEY UPDATE setting_value = :update_value');
            $themeStatement->execute([
                ':key' => 'default_theme',
                ':insert_value' => $defaultTheme,
                ':update_value' => $defaultTheme,
            ]);

            foreach ($imageSettings as $field => $imagePath) {
                $imageStatement = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :insert_value) ON DUPLICATE KEY UPDATE setting_value = :update_value');
                $imageStatement->execute([
                    ':key' => $field,
                    ':insert_value' => $imagePath,
                    ':update_value' => $imagePath,
                ]);
            }

            $message = 'Settings updated successfully.';
        }
    }
}

$settings = getSiteSettings($pdo);
$token = generateAdminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0b1220; color: #f8fafc; font-family: Arial, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; padding: 24px 18px; }
        .sidebar a { display: block; color: #e5e7eb; text-decoration: none; padding: 12px 14px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a:hover { background: #1f2937; }
        .main { flex: 1; padding: 30px; }
        .panel { background: #1f2937; border-radius: 12px; padding: 22px; margin-bottom: 24px; }
        input, textarea, select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: white; margin-top: 8px; }
        button { padding: 10px 14px; border: none; background: #f4b400; color: #111; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .field-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <h2>Admin</h2>
            <a href="dashboard.php">Dashboard</a>
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
            <h1>Website Settings</h1>
            <?php if ($message !== ''): ?><p class="success"><?php echo e($message); ?></p><?php endif; ?>
            <?php if ($error !== ''): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
            <div class="panel">
                <form method="post" action="settings.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                    <div class="field-grid">
                        <div>
                            <label>Display Name</label>
                            <input name="name" maxlength="120" value="<?php echo e($settings['name']); ?>" required>
                        </div>
                        <div>
                            <label>Website Title</label>
                            <input name="title" value="<?php echo e($settings['title']); ?>">
                        </div>
                        <div>
                            <label>Default Appearance</label>
                            <select name="default_theme">
                                <option value="dark" <?php echo $settings['default_theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                                <option value="light" <?php echo $settings['default_theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                            </select>
                        </div>
                        <div>
                            <label>Intro</label>
                            <input name="intro" value="<?php echo e($settings['intro']); ?>">
                        </div>
                        <div>
                            <label>Tagline</label>
                            <input name="tagline" value="<?php echo e($settings['tagline']); ?>">
                        </div>
                        <div>
                            <label>Email</label>
                            <input name="email" value="<?php echo e($settings['email']); ?>">
                        </div>
                        <div>
                            <label>Location</label>
                            <input name="location" value="<?php echo e($settings['location']); ?>">
                        </div>
                        <div>
                            <label>Facebook</label>
                            <input name="facebook" value="<?php echo e($settings['facebook']); ?>">
                        </div>
                        <div>
                            <label>Instagram</label>
                            <input name="instagram" value="<?php echo e($settings['instagram']); ?>">
                        </div>
                        <div>
                            <label>Profile Image (JPG, PNG, or GIF; max 2MB)</label>
                            <input type="file" name="profile_image" accept="image/png,image/jpeg,image/gif">
                        </div>
                        <div>
                            <label>Site Logo (JPG, PNG, or GIF; max 2MB)</label>
                            <input type="file" name="logo_image" accept="image/png,image/jpeg,image/gif">
                            <?php if ($settings['logo_image'] !== ''): ?>
                                <img src="../<?php echo e($settings['logo_image']); ?>" alt="Current site logo" style="max-width: 180px; max-height: 80px; margin-top: 10px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="margin-top: 18px;">
                        <label>Bio</label>
                        <textarea name="bio" rows="5"><?php echo e($settings['bio']); ?></textarea>
                    </div>
                    <p style="margin-top: 18px;"><button type="submit">Save Settings</button></p>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
