CREATE DATABASE IF NOT EXISTS personal_portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE personal_portfolio;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(64) NOT NULL PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_login_attempts (
    attempt_key CHAR(64) NOT NULL PRIMARY KEY,
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    last_attempt INT UNSIGNED NOT NULL,
    KEY idx_admin_login_attempts_last_attempt (last_attempt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_skills_category_name (category, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    summary TEXT NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL DEFAULT 'images/project1.jpg',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS education (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    institution VARCHAR(180) NOT NULL,
    period VARCHAR(80) NOT NULL,
    details TEXT NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experiences (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    company VARCHAR(180) NOT NULL,
    period VARCHAR(80) NOT NULL,
    details TEXT NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS achievements (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    issuer VARCHAR(180) NOT NULL,
    issue_date VARCHAR(80) NOT NULL,
    reference VARCHAR(200) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0
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
    ('name', 'Chynie Saguban'),
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

INSERT INTO projects (title, summary, description, image_url, sort_order) VALUES
    ('Tutoring System & Student Analytics Platform', 'A web-based tutoring management system that connects students with tutors and tracks academic progress.', 'The platform helps manage sessions, view performance trends, and store learning data in a structured MySQL database.', 'images/project1.jpg', 1),
    ('NGO Child Protection Resource Database', 'A relational database created to organize advocacy resources and outreach records.', 'The project supports child protection efforts by capturing service data and making it easier to monitor programs and resources.', 'images/project2.jpg', 2),
    ('Student Academic Progress Tracking System', 'A database-driven academic dashboard for monitoring students and assessments.', 'It stores performance metrics and generates summaries that help tutors and instructors understand student growth.', 'images/project3.jpg', 3),
    ('Hybrid Network Topology Simulations', 'A series of network simulations designed in Cisco Packet Tracer.', 'Each project demonstrates routing, redundancy, and topology planning for campus-style network infrastructures.', 'images/project4.jpg', 4)
ON DUPLICATE KEY UPDATE
    summary = VALUES(summary),
    description = VALUES(description),
    image_url = VALUES(image_url),
    sort_order = VALUES(sort_order);

INSERT INTO education (title, institution, period, details, sort_order) VALUES
    ('Bachelor of Science in Information Technology', 'Negros Oriental State University – Bayawan-Sta. Catalina Campus', '2024 – 2028 (Expected)', 'Focused on data systems, programming, and technology-based learning solutions.', 1),
    ('Senior High School Diploma', 'Bayawan National High School – Senior High School', '2-Year Duration', 'Completed senior high school studies with a strong interest in technology and academic performance.', 2),
    ('Junior High School Diploma', 'Bayawan National High School – Junior High School', '4-Year Duration', 'Built foundational skills and developed interest in technology and communication.', 3),
    ('Elementary Education', 'Bayawan City East Central School', 'Grades 1 – 6', 'Completed primary education with a focus on discipline, learning, and academic growth.', 4)
ON DUPLICATE KEY UPDATE
    institution = VALUES(institution),
    period = VALUES(period),
    details = VALUES(details),
    sort_order = VALUES(sort_order);

INSERT INTO experiences (title, company, period, details, sort_order) VALUES
    ('Part-Time Tutor', 'Academic & Tutoring Services', 'Ongoing', 'Providing individualized learning support to students and evaluating learning progress.', 1),
    ('Student Volunteer / Advocate', 'Child Protection Community Initiatives', 'Ongoing', 'Supporting outreach campaigns and helping promote child welfare and protection.', 2)
ON DUPLICATE KEY UPDATE
    company = VALUES(company),
    period = VALUES(period),
    details = VALUES(details),
    sort_order = VALUES(sort_order);

INSERT INTO achievements (title, issuer, issue_date, reference, sort_order) VALUES
    ('AI for Beginners', 'HP Foundation — HP LIFE', 'September 27, 2026', 'Serial: 927e8c7c-3cdb-404f-83f1-80752964900e', 1),
    ('Professional Networking for Career Growth', 'HP Foundation — HP LIFE', 'September 27, 2026', 'Serial: eb7daf8c-b1f0-4876-a761-8702a5d406d7', 2),
    ('Artificial Intelligence (AI) for Social Impact', 'ADBI E-Learning', 'September 27, 2026', 'Certificate ID: 199746-179-052-1005', 3),
    ('Cybersecurity', 'ADBI E-Learning', 'September 28, 2026', 'Certificate ID: 199746-179-052-1721', 4),
    ('JavaScript Essentials 1', 'DICT-ITU DTC Initiative • Cisco Networking Academy', 'Successfully Completed', 'Cisco Networking Academy Program', 5)
ON DUPLICATE KEY UPDATE
    issuer = VALUES(issuer),
    issue_date = VALUES(issue_date),
    reference = VALUES(reference),
    sort_order = VALUES(sort_order);
