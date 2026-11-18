CREATE DATABASE IF NOT EXISTS personal_portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE personal_portfolio;

CREATE TABLE IF NOT EXISTS administrators (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS personal_info (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    title VARCHAR(150) NOT NULL,
    introduction TEXT NOT NULL,
    biography TEXT NOT NULL,
    email VARCHAR(150) NOT NULL,
    facebook VARCHAR(255) NOT NULL DEFAULT '',
    instagram VARCHAR(255) NOT NULL DEFAULT '',
    profile_image VARCHAR(255) NOT NULL DEFAULT 'images/profile.png',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS website_settings (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    website_title VARCHAR(200) NOT NULL,
    logo_text VARCHAR(50) NOT NULL DEFAULT 'CD',
    primary_color CHAR(7) NOT NULL DEFAULT '#5b5ce2',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    percentage TINYINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_skills_percentage CHECK (percentage <= 100)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    technology VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    project_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_unread (is_read),
    INDEX idx_messages_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS education (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(200) NOT NULL,
    degree VARCHAR(200) NOT NULL,
    start_year VARCHAR(20) NOT NULL,
    end_year VARCHAR(20) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS experiences (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    organization VARCHAR(200) NOT NULL,
    start_year VARCHAR(20) NOT NULL,
    end_year VARCHAR(20) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS achievements (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    issuer VARCHAR(200) NOT NULL DEFAULT '',
    achievement_date VARCHAR(50) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO personal_info (id, full_name, title, introduction, biography, email, facebook, instagram, profile_image)
VALUES (
    1,
    'Charles Daryll P. Tamayo',
    'Student',
    'I am still improving my skills and discovering what I enjoy the most.',
    'This portfolio highlights my education, creative work, and experience.',
    'tamayocharlesdaryll@gmail.com',
    'https://www.facebook.com/charles.tamayo.522',
    'https://www.instagram.com/charles.jpeg',
    'images/profile.png'
)
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO website_settings (id, website_title, logo_text, primary_color)
VALUES (1, 'Charles Daryll P. Tamayo | Portfolio', 'CD', '#5b5ce2')
ON DUPLICATE KEY UPDATE id = id;
