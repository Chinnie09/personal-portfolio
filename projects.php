<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$projects = getProjects($pdo);
$settings = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | <?php echo e($settings['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('projects', $settings['name']); ?>
<main>
    <section class="page-section">
        <div class="section-title">
            <p class="small-title">My Academic &amp; Personal Works</p>
            <h1>Projects</h1>
        </div>
        <div class="projects-container">
            <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <img src="<?php echo e($project['image_url']); ?>" alt="<?php echo e($project['title']); ?>">
                    <div class="project-content">
                        <h2><?php echo e($project['title']); ?></h2>
                        <p><?php echo e($project['summary']); ?></p>
                        <p><?php echo nl2br(e($project['description'])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if ($projects === []): ?>
                <p>No projects have been added yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php renderFooter(); ?>
</body>
</html>
