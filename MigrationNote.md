# Migration Note

**Important:** First time loading the project (like on login page), the system will automatically create the database, create the `users` table, and insert a default user. No manual SQL import is needed.

### Default User Seeded Automatically:

- Name: Md. Muhibbur Rahman
- Email: muhib2929@gmail.com
- Password: 12345678 (hashed)

### Manual SQL (if needed)

If for some reason the automatic migration doesn't work, you can run this SQL manually:

### Purpose

- Automatically creates the database and users table if they don’t exist.
- Seeds a default user for quick testing/login.

### Default DB and Table

- Database: `authdb`
- Table: `users`

| Field      | Type         | Notes                       |
| ---------- | ------------ | --------------------------- |
| id         | INT UNSIGNED | AUTO_INCREMENT, PRIMARY KEY |
| username   | VARCHAR(32)  | Not Null                    |
| email      | VARCHAR(32)  | Not Null, UNIQUE            |
| password   | VARCHAR(255) | Not Null                    |
| created_at | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP   |

### Seeder

- Default user:
  - Username: `Md. Muhibbur Rahman`
  - Email: `muhib2929@gmail.com`
  - Password: `12345678` (hashed automatically)

### Flow

1. `Core.php` constructor tries to connect to the DB.
2. If connection fails (DB not found):
   - `migration.php` is required.
   - Migration class creates DB and table.
   - Seeder adds default user.
   - Core reconnects automatically.

### Notes

- Safe to run multiple times — no duplicate tables or users.
- Ideal for first-time setup or resetting DB.

---

## SQL Manual Option (if auto migration fails)

```sql
-- STEP-01: Create Database
CREATE DATABASE IF NOT EXISTS `authdb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- STEP-02: Use Database
USE `authdb`;


-- STEP-03: Create Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(32) NOT NULL,
    `email` VARCHAR(32) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- STEP-04: Create an  user (example SQL, Optional)
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('Md. Muhibbur Rahman', 'muhib2929@gmail.com', '$2y$10$YCfohWJf1XCM0p43k1rG2Oa4gfY2yqSMtOjRbIwHSipnTcE25RqbC');


-- Username: Md. Muhibbur Rahman
-- Email: muhib2929@gmail.com
-- Password: 12345678

```
