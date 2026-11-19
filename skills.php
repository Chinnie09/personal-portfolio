<?php
session_start();
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$skills = getSkills($pdo);
$settings = getSiteSettings($pdo);
$groupedSkills = [];
foreach ($skills as $skill) {
    $groupedSkills[$skill['category']][] = $skill;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills | <?php echo e($settings['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('skills', $settings['name']); ?>
    <main>
        <section class="page-section">
            <div class="section-title">
                <p class="small-title">My Technical Expertise & Tools</p>
                <h1>Skills</h1>
            </div>
            <?php foreach ($groupedSkills as $category => $items): ?>
                <h2 class="skill-category-title"><?php echo e($category); ?></h2>
                <div class="skills-container">
                    <?php foreach ($items as $skill): ?>
                        <div class="skill-card">
                            <h2><?php echo e($skill['name']); ?></h2>
                            <p><?php echo e($skill['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
<?php renderFooter(); ?>
</body>
</html>
