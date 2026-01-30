# Assignment : 02

### Name : Md. Muhibbur Rahman

### Email: muhib2929@gmail.com

---

## Project: User Authentication & Profile Management System

### Technologies Used:

- PHP (Object-Oriented Programming – OOP)
- MySQL
- HTML/CSS/TailwindCSS

---

## Project Overview

This project implements a **secure user authentication and profile management system**. Users can:

1. Register with a unique email and hashed password
2. Login securely using sessions
3. View and update their profile (username, email, optional password)
4. Logout with proper session and cookie cleanup

All database operations use **prepared statements** to prevent SQL injection, and passwords are hashed with `password_hash`.

---

## System Design

- **Core Class (`core.php`)**: Handles all main operations like signup, login, logout, fetch, update profile, and update password.
- **Middleware (`middleware.php`)**: Manages session and authentication checks for secure page access.
- **Trigger File (`trigger.php`)**: Central file that handles all form submissions and connects user actions to Core methods.
- **Helper Functions (`helper.php`)**: Utility functions for validation, alerts, and sanitization.
- **Migration & Auto-Setup (`migration.php`)**: Core constructor automatically runs migration on first load to create database, users table, and seed default user if not exists

---

## Features

- User registration with validation and password hashing
- Login with session management and optional "Remember Me" cookie
- Profile viewing and updating (username, email, password)
- Secure logout
- Input validation and SQL injection protection

---

## Database Design

**Database Name:** `authdb`

**Table: users**
| Field | Type | Notes |
|------------|-------------|------------------------------|
| id | INT | Primary Key, Auto Increment |
| username | VARCHAR(255)| User full name |
| email | VARCHAR(255)| Unique, login email |
| password | VARCHAR(255)| Hashed password |
| created_at | TIMESTAMP | Default current timestamp |

---

**NOTE:** This project includes a migration system that automatically creates the database, users table, and seeds a default user if the connection fails. For more details, see the `MigrateNote.md` file.

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone -b main https://github.com/Muhib68442/InteractiveCares-Assignment-02
   ```
2. Start XAMPP and place the project in your htdocs folder:
   ```bash
   cd InteractiveCares-Assignment-02
   ```
3. Open your browser and navigate to:
   ```bash
   http://localhost/InteractiveCares-Assignment-02/login.php
   ```

**NOTE:** The project automatically creates the database, users table, and seeds a default user if the database is not found. For details, see `MigrateNote.md`.

**NOTE:** Theres a copy of Database `authdb.sql` with the directory. You can import it into your phpmyadmin as mysql database.
