<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$education = getEducation($pdo);
$experiences = getExperiences($pdo);
$achievements = getAchievements($pdo);
$settings = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume | <?php echo e($settings['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('resume', $settings['name'], $settings['logo_image'], $settings['default_theme']); ?>
<main>
    <section class="page-section">
        <div class="section-title">
            <p class="small-title">My Academic &amp; Professional Journey</p>
            <h1>Resume</h1>
        </div>
        <div class="resume-container">
            <div class="resume-block">
                <h2>Education History</h2>
                <?php foreach ($education as $record): ?>
                    <div class="resume-item">
                        <h3><?php echo e($record['title']); ?></h3>
                        <p class="institution"><?php echo e($record['institution']); ?></p>
                        <p class="year"><?php echo e($record['period']); ?></p>
                        <p><?php echo e($record['details']); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if ($education === []): ?><p>No education records have been added yet.</p><?php endif; ?>
            </div>
            <div class="resume-block">
                <h2>Work &amp; Volunteer Experience</h2>
                <?php foreach ($experiences as $experience): ?>
                    <div class="resume-item">
                        <h3><?php echo e($experience['title']); ?></h3>
                        <p class="institution"><?php echo e($experience['company']); ?></p>
                        <p class="year"><?php echo e($experience['period']); ?></p>
                        <p><?php echo e($experience['details']); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if ($experiences === []): ?><p>No experience records have been added yet.</p><?php endif; ?>
            </div>
            <div class="resume-block">
                <h2>Certifications &amp; Achievements</h2>
                <div class="certificates-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <article class="certificate-card">
                            <div class="cert-issuer"><?php echo e($achievement['issuer']); ?></div>
                            <h3><?php echo e($achievement['title']); ?></h3>
                            <p class="cert-date"><?php echo e($achievement['issue_date']); ?></p>
                            <p class="cert-id"><?php echo e($achievement['reference']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($achievements === []): ?><p>No achievements have been added yet.</p><?php endif; ?>
            </div>
        </div>
    </section>
</main>
<?php renderFooter(); ?>
</body>
</html>
