# Open Source Applications - Setup Guide

## Prerequisites
- Linux OS (Ubuntu/Debian recommended)
- PHP 8.1+
- MySQL 8.0+ / MariaDB
- Web Server (Apache/Nginx)
- PHP Extensions: `pdo_mysql`, `fileinfo`, `session`

## Installation Steps

### 1. Database Setup
Create a new MySQL database named `osa_studio` and import the schema and seed data.
```bash
mysql -u root -p -e "CREATE DATABASE osa_studio;"
mysql -u root -p osa_studio < db/database.sql
mysql -u root -p osa_studio < db/seed.sql
```

### 2. Configure Constants
Open `includes/config.php` and update the database credentials and `BASE_URL`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'osa_studio');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');

define('BASE_URL', 'http://localhost/osaapp');
```

### 3. Folder Permissions
Ensure the `uploads` directory is writable by the web server user (e.g., `www-data`).
```bash
chmod -R 775 uploads
chown -R www-data:www-data uploads
```

### 4. Admin Credentials
The default admin account created by `seed.sql`:
- **Username:** `admin`
- **Password:** `admin123`
- **Link:** `BASE_URL/admin/login.php`

## Project Structure
- `/public`: User-facing pages (index, projects, submit, etc.).
- `/admin`: Studio moderation and management dashboard.
- `/includes`: Business logic, security helpers, and DB connection.
- `/partials`: Reusable UI components (header, footer, admin sidebar).
- `/uploads`: Storage for logos, screenshots, and binaries.
- `/assets`: Frontend CSS/JS and branding images.
