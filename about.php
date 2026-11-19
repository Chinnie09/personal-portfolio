<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$settings = getSiteSettings($pdo);
$education = getEducation($pdo);
$experiences = getExperiences($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me | <?php echo e($settings['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('about', $settings['name'], $settings['logo_image'], $settings['default_theme']); ?>
    <main>
        <section class="page-section">
            <div class="section-title">
                <p class="small-title">Get To Know Me</p>
                <h1>About Me</h1>
            </div>
            <div class="about-container">
                <div class="about-text">
                    <h2>Hello! I'm <?php echo e($settings['name']); ?></h2>
                    <p><?php echo nl2br(e($settings['bio'])); ?></p>
                    <?php if ($education !== []): ?>
                        <h3>Education</h3>
                        <?php foreach ($education as $record): ?>
                            <p><strong><?php echo e($record['title']); ?></strong><br>
                                <?php echo e($record['institution']); ?> · <?php echo e($record['period']); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($experiences !== []): ?>
                        <h3>Experience &amp; Activities</h3>
                        <?php foreach ($experiences as $experience): ?>
                            <p><strong><?php echo e($experience['title']); ?></strong> — <?php echo e($experience['company']); ?> (<?php echo e($experience['period']); ?>)<br>
                                <?php echo e($experience['details']); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
<?php renderFooter(); ?>
</body>
</html>
