<?php
declare(strict_types=1);

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getSiteSettings(PDO $pdo): array
{
    $settings = [
        'name' => 'Chynie Saguban',
        'title' => 'Chynie Saguban | IT Student Portfolio',
        'intro' => 'Welcome to my portfolio',
        'tagline' => 'IT Student & Aspiring Data Analyst',
        'bio' => 'I am Chynie Saguban, currently studying BS Information Technology at Negros Oriental State University – Bayawan-Sta. Catalina Campus. My interests include database systems, educational technology, and data-driven solutions that support student success.',
        'profile_image' => 'images/profile.jpg',
        'email' => 'chyniesaguban696@gmail.com',
        'location' => 'Bayawan City, Negros Oriental, Philippines',
        'facebook' => 'https://www.facebook.com/chynie.saguban.73',
        'instagram' => 'https://www.instagram.com/_fschi.n',
    ];

    $statement = $pdo->query('SELECT setting_key, setting_value FROM site_settings');
    foreach ($statement->fetchAll() as $row) {
        if (array_key_exists($row['setting_key'], $settings)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings;
}

function getSkills(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT category, name, description FROM skills ORDER BY sort_order, id'
    );

    return $statement->fetchAll();
}

function getProjects(PDO $pdo): array
{
    $statement = $pdo->query('SELECT title, summary, description, image_url FROM projects ORDER BY sort_order, id');

    return $statement->fetchAll();
}

function getEducation(PDO $pdo): array
{
    $statement = $pdo->query('SELECT title, institution, period, details FROM education ORDER BY sort_order, id');

    return $statement->fetchAll();
}

function getExperiences(PDO $pdo): array
{
    $statement = $pdo->query('SELECT title, company, period, details FROM experiences ORDER BY sort_order, id');

    return $statement->fetchAll();
}

function getAchievements(PDO $pdo): array
{
    $statement = $pdo->query('SELECT title, issuer, issue_date, reference FROM achievements ORDER BY sort_order, id');

    return $statement->fetchAll();
}

function renderPublicHeader(string $activePage, string $siteName = 'Chynie Saguban'): void
{
    $pages = [
        'index' => ['Home', 'index.php'],
        'about' => ['About', 'about.php'],
        'skills' => ['Skills', 'skills.php'],
        'projects' => ['Projects', 'projects.php'],
        'resume' => ['Resume', 'resume.php'],
        'contact' => ['Contact', 'contact.php'],
    ];

    echo '<header><nav class="navbar">';
    echo '<div class="logo">' . e($siteName) . '</div>';
    echo '<button class="menu-toggle" id="menu-toggle" type="button" aria-label="Toggle navigation">☰</button>';
    echo '<ul class="nav-links" id="nav-links">';

    foreach ($pages as $page => [$label, $href]) {
        $activeClass = $page === $activePage ? ' class="active"' : '';
        echo '<li><a href="' . e($href) . '"' . $activeClass . '>' . e($label) . '</a></li>';
    }

    echo '</ul></nav></header>';
}

function renderFooter(): void
{
    echo '<footer><p>© 2026 Chynie Saguban. All Rights Reserved.</p></footer>';
    echo '<button id="scroll-top" type="button" aria-label="Scroll to top">↑</button>';
    echo '<button id="theme-toggle" type="button" title="Toggle theme" aria-label="Toggle theme">☀</button>';
    echo '<script src="js/script.js"></script>';
}

function sanitizeText(mixed $value): string
{
    if (!is_string($value)) {
        return '';
    }

    return trim(strip_tags($value));
}

function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function hasBotTrap(array $post): bool
{
    return isset($post['website'])
        && is_string($post['website'])
        && trim($post['website']) !== '';
}
