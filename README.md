# Chynie Saguban — Personal Portfolio

A responsive personal portfolio built with HTML, CSS, and JavaScript for the ITS 300-B Midterm Project.

## Live Website

[Visit the portfolio](https://chinnie09.github.io/personal-portfolio/)

## Pages

- Home — Introduction, profile photo, and featured links
- About — Background, education, interests, and career goals
- Skills — Skills grouped into four categories
- Projects — Four academic and personal projects
- Resume — Education, experience, networking activities, and certifications
- Contact — Contact details, social links, and a client-side validated form

## Features

- Responsive layouts for desktop, tablet, and mobile
- PHP pages that read portfolio settings, skills, projects, education, experience, and achievements from MySQL
- Admin CRUD for skills, projects, education, experience, and achievements
- Admin settings for profile information and profile image uploads
- Project image uploads validated as JPG, PNG, or GIF files up to 2MB
- Contact messages saved to MySQL and managed from the dashboard
- Mobile navigation menu
- Dark and light themes, with the selected theme saved in local storage
- Animated typing effect on the home page
- Scroll-to-top button and active-page navigation highlighting
- Project image preview modal
- Scroll-reveal animations
- Contact form input validation

The HTML contact form only validates the entered information in the browser; it does not send or store messages. The PHP contact page uses a PDO prepared statement and a CSRF token to store messages in MySQL.

## Technologies

- HTML5
- CSS3, including custom properties, Flexbox, Grid, animations, and transitions
- JavaScript (ES6), including DOM APIs, `IntersectionObserver`, and local storage
- PHP with PDO and MySQL for the server-side pages and contact form

## Requirements

- PHP 8.0 or newer with PDO MySQL enabled
- MySQL or MariaDB
- Apache or another web server that can run PHP
- A writable `uploads` directory; Apache deployments must apply the provided `.htaccess` rules there

## Run Locally

Open `index.html` to view the original static midterm site. To use the database-backed version, run the PHP application through Apache.

GitHub Pages hosts only the static HTML site; it does not run PHP. To run the PHP pages locally:

1. Start Apache and MySQL in XAMPP, then import [`database/schema.sql`](./database/schema.sql) in phpMyAdmin.
2. Copy `includes/config.example.php` to `includes/config.local.php` and update the database credentials.
3. Ensure the `pdo_mysql` PHP extension is enabled, then open `index.php` through the local Apache server.

The local database configuration is ignored by Git and should not be committed.
If you already imported the schema, run the `admin_login_attempts` table statement from `database/schema.sql` in phpMyAdmin before using the admin login.

## Admin Dashboard

Import `database/schema.sql`, configure the database connection, then visit `admin/login.php`. The dashboard includes content management for skills, projects, education, experience, and achievements; message management; and profile and website settings.

No administrator password is seeded in the SQL file. Create an administrator with a unique password hash generated locally using PHP's `password_hash()` and insert the username, hash, name, and email into `admin_users`. Keep the password and database credentials private.

The database-backed site requires PHP and MySQL hosting. GitHub Pages only serves the original static HTML and cannot run the PHP application, so deployment of the dynamic version requires a PHP-capable host.

## Author

Chynie Saguban — BS Information Technology, 3rd Year at Negros Oriental State University — Bayawan-Sta. Catalina Campus

- Email: [chyniesaguban696@gmail.com](mailto:chyniesaguban696@gmail.com)
- Location: Bayawan City, Negros Oriental, Philippines
- Facebook: [Chynie Saguban](https://www.facebook.com/chynie.saguban.73)
- Instagram: [@_fschi.n](https://www.instagram.com/_fschi.n)

## License

© 2026 Chynie Saguban. All rights reserved.