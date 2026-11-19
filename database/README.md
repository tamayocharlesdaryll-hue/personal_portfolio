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
