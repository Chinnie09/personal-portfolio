<?php
session_start();
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$settings = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($settings['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('index', $settings['name'], $settings['logo_image'], $settings['default_theme']); ?>
    <main>
        <section class="hero">
            <div class="hero-text">
                <p class="small-title"><?php echo e($settings['intro'] ?: 'Welcome to my portfolio'); ?></p>
                <h1>
                    Hi, I'm <span><?php echo e($settings['name']); ?></span>
                </h1>
                <h2>
                    <span id="typing-text"><?php echo e($settings['tagline'] ?: 'IT Student & Aspiring Data Analyst'); ?></span>
                </h2>
                <p>
                    <?php echo e($settings['bio'] ?: 'I am Chynie Saguban, currently studying BS Information Technology at Negros Oriental State University – Bayawan-Sta. Catalina Campus. My interests include database systems, educational technology, and data-driven solutions that support student success.'); ?>
                </p>
                <a href="projects.php" class="btn">View My Projects</a>
            </div>
            <div class="hero-image">
                <img src="<?php echo e($settings['profile_image'] ?: 'images/profile.jpg'); ?>" alt="<?php echo e($settings['name']); ?> Profile Picture">
            </div>
        </section>
    </main>
<?php renderFooter(); ?>
</body>
</html>
