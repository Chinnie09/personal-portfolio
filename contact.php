<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/security.php';

startSecureSession();
$pdo = getDbConnection();
$settings = getSiteSettings($pdo);
$formStatus = '';
$formError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    if (!verifyCsrfToken($token)) {
        $formStatus = 'Security validation failed. Please try again.';
        $formError = true;
    } elseif (hasBotTrap($_POST)) {
        $formStatus = 'The request was rejected as spam.';
        $formError = true;
    } else {
        $name = sanitizeText($_POST['name'] ?? '');
        $email = sanitizeText($_POST['email'] ?? '');
        $subject = sanitizeText($_POST['subject'] ?? '');
        $message = sanitizeText($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $subject === '' || $message === '' || !isValidEmail($email) || strlen($message) < 10) {
            $formStatus = 'Please complete all fields with valid information.';
            $formError = true;
        } else {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, 'unread')");
            $stmt->execute([$name, $email, $subject, $message]);
            $formStatus = 'Thank you. Your message has been sent successfully.';
            $formError = false;
            $_POST = [];
        }
    }
}

$token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Chynie Saguban Portfolio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php renderPublicHeader('contact', $settings['name']); ?>
    <main>
        <section class="page-section">
            <div class="section-title">
                <p class="small-title">Get In Touch</p>
                <h1>Contact Me</h1>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Direct Contact Details</h2>
                    <p>Feel free to reach out for tutoring inquiries, data analysis collaborations, or project discussions.</p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo e($settings['email']); ?>"><?php echo e($settings['email']); ?></a></p>
                    <p><strong>Location:</strong> <?php echo e($settings['location']); ?></p>
                    <h3>Social Links</h3>
                    <ul>
                        <li><strong>Facebook:</strong> <a href="<?php echo e($settings['facebook']); ?>" target="_blank" rel="noopener noreferrer">Chynie Saguban</a></li>
                        <li><strong>Instagram:</strong> <a href="<?php echo e($settings['instagram']); ?>" target="_blank" rel="noopener noreferrer">@_fschi.n</a></li>
                    </ul>
                </div>
                <form class="contact-form" method="post" action="contact.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e($token); ?>">
                    <input type="hidden" name="website" value="">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required value="<?php echo e($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?php echo e($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter subject" required value="<?php echo e($_POST['subject'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required><?php echo e($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn">Send Message</button>
                    <?php if ($formStatus !== ''): ?>
                        <p class="form-status" style="color: <?php echo $formError ? '#e74c3c' : '#27ae60'; ?>"><?php echo e($formStatus); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </main>
<?php renderFooter(); ?>
</body>
</html>
