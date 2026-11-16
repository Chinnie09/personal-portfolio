CREATE DATABASE IF NOT EXISTS personal_portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE personal_portfolio;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(64) NOT NULL PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_skills_category_name (category, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'unread',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('title', 'Chynie Saguban | IT Student Portfolio'),
    ('intro', 'Welcome to my portfolio'),
    ('tagline', 'IT Student & Aspiring Data Analyst'),
    ('bio', 'I am Chynie Saguban, currently studying BS Information Technology at Negros Oriental State University – Bayawan-Sta. Catalina Campus. My interests include database systems, educational technology, and data-driven solutions that support student success.'),
    ('profile_image', 'images/profile.jpg'),
    ('email', 'chyniesaguban696@gmail.com'),
    ('location', 'Bayawan City, Negros Oriental, Philippines'),
    ('facebook', 'https://www.facebook.com/chynie.saguban.73'),
    ('instagram', 'https://www.instagram.com/_fschi.n')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO skills (category, name, description, sort_order) VALUES
    ('Programming & Database', 'C++', 'Object-oriented programming, data structures, and algorithm logic fundamentals.', 1),
    ('Programming & Database', 'PHP', 'Server-side scripting to connect dynamic web platforms with backend databases.', 2),
    ('Programming & Database', 'MySQL', 'Database design, querying, data normalization, and relational database management.', 3),
    ('Design & Multimedia', 'Canva', 'Designing visual presentations, social media graphics, and promotional materials.', 4),
    ('Design & Multimedia', 'Adobe Illustrator', 'Creating vector graphics, logos, and scalable design assets.', 5),
    ('Design & Multimedia', 'Adobe Animate', 'Producing 2D animations and interactive multimedia content.', 6),
    ('Productivity & Documentation', 'Microsoft Word', 'Professional document creation, formatting, and technical reports.', 7),
    ('Productivity & Documentation', 'Microsoft PowerPoint', 'Designing compelling slide decks for presentations and academic reports.', 8),
    ('Productivity & Documentation', 'Microsoft Excel', 'Data cleaning, pivot tables, functions, and preliminary data analysis.', 9),
    ('Networking & Infrastructure', 'Cisco Packet Tracer', 'Designing hybrid network topologies and simulating routing configurations.', 10)
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    sort_order = VALUES(sort_order);
