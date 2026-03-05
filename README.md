# Excelfix Webapp

Simple PHP registration/login webapp for local development.

## Requirements

- XAMPP (Apache + MySQL)
- PHP 8+ (bundled with XAMPP)

## Setup

1. Copy the project to your XAMPP web root, e.g. `c:\xampp\htdocs\Excelfix`.
2. Start Apache and MySQL in XAMPP.
3. Create the database `excel_fix` in phpMyAdmin or MySQL.
4. Create a `users` table:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Run

Open in your browser:

- http://localhost/Excelfix/Webapp/register.php
- http://localhost/Excelfix/Webapp/index.php

## Configuration

Database settings are in `register.php` and `index.php`:

- DB_HOST
- DB_NAME
- DB_USER
- DB_PASS

## Notes

- Passwords are stored as hashes using `password_hash`.
- Validation errors are shown inline on the register page.
