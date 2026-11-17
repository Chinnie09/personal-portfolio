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

## Run Locally

Open `index.html` in a browser, or serve the project directory with any static web server. The static HTML site does not require a build step or package installation.

GitHub Pages hosts only the static HTML site; it does not run PHP. To run the PHP pages locally:

1. Start Apache and MySQL in XAMPP, then import [`database/schema.sql`](./database/schema.sql) in phpMyAdmin.
2. Copy `includes/config.example.php` to `includes/config.local.php` and update the database credentials.
3. Ensure the `pdo_mysql` PHP extension is enabled, then open `index.php` through the local Apache server.

The local database configuration is ignored by Git and should not be committed.
If you already imported the schema, run the `admin_login_attempts` table statement from `database/schema.sql` in phpMyAdmin before using the admin login.

## Admin Dashboard (in progress)

Import `database/schema.sql`, configure the database connection, then visit `admin/login.php`. No administrator password is seeded in the SQL file. Create an administrator with a unique password hash generated locally using PHP's `password_hash()` and insert the username, hash, name, and email into `admin_users`. Keep the password and database credentials private.

The initial dashboard includes login/logout, counts, skills and project management, message status/deletion, website settings, and profile image upload. Remaining portfolio content modules and public-page database rendering are being added in later development steps.

## Author

Chynie Saguban — BS Information Technology, 3rd Year at Negros Oriental State University — Bayawan-Sta. Catalina Campus

- Email: [chyniesaguban696@gmail.com](mailto:chyniesaguban696@gmail.com)
- Location: Bayawan City, Negros Oriental, Philippines
- Facebook: [Chynie Saguban](https://www.facebook.com/chynie.saguban.73)
- Instagram: [@_fschi.n](https://www.instagram.com/_fschi.n)

## License

© 2026 Chynie Saguban. All rights reserved.