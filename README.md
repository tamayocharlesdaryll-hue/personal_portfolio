# PHP and MySQL setup

The PHP pages use `includes/db.php` to create a PDO connection. Configure the following environment variables for the PHP process:

- `DB_HOST` (optional; defaults to `127.0.0.1`)
- `DB_PORT` (optional; defaults to `3306`)
- `DB_NAME` (optional; defaults to `personal_portfolio`)
- `DB_USER` (required)
- `DB_PASSWORD` (optional for local MySQL accounts with no password)

Create the database tables and default portfolio profile/settings by importing `database/schema.sql` into MySQL. Do not commit production database credentials.

Create the first administrator yourself after importing the schema. Generate a password hash with PHP's `password_hash()` and insert the hash, name, and email into `administrators`; the application intentionally provides no default admin credentials.

The PHP runtime needs PDO MySQL enabled. Contact form length validation uses the PHP `mbstring` extension.

# Personal Portfolio Website

A dynamic personal portfolio website built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### Public Website
- Dynamic personal profile
- Skills section
- Projects showcase with images
- Education history
- Work experience
- Achievements
- Contact form

### Admin Dashboard
- Admin login and logout
- Profile management
- Skills management
- Projects management and image uploads
- Education management
- Experience management
- Achievements management
- Messages management
- Website settings

## Installation

1. Install XAMPP with PHP and MySQL.
2. Copy the project folder into `htdocs`.
3. Create a MySQL database.
4. Import `database/schema.sql` using phpMyAdmin.
5. Configure the database connection in `includes/db.php` using the required environment variables.
6. Create an administrator account using a securely generated password hash.
7. Start Apache and MySQL in XAMPP.
8. Open the project through `http://localhost/personal_portfolio/`.

## Live Website

https://tamayocharles.free.nf/

## Database

The database schema is located at `database/schema.sql`.

Never publish production database credentials or administrator passwords in this repository.
