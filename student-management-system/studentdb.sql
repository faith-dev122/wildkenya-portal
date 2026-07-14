-- BIT3208 Student Management System Database
-- Import this file in phpMyAdmin (Import tab) or run in SQL tab.

CREATE DATABASE IF NOT EXISTS studentdb;
USE studentdb;

-- Users table (Week 7 & 9: authentication, sessions)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table (Weeks 6, 10, 11: CRUD)
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_no VARCHAR(30) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  course VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample student records
INSERT INTO students (reg_no, full_name, email, course) VALUES
('BSCCS/2024/001', 'John Kamau',    'john.kamau@example.com',  'Computer Science'),
('BSCIT/2024/014', 'Mary Wanjiru',  'mary.wanjiru@example.com','Information Technology'),
('BSCCS/2024/027', 'Peter Otieno',  'peter.otieno@example.com','Computer Science'),
('BSCSE/2024/033', 'Grace Achieng', 'grace.a@example.com',     'Software Engineering'),
('BSCIT/2024/045', 'Ali Hassan',    'ali.hassan@example.com',  'Information Technology');

-- NOTE: Create user accounts through register.php so passwords are
-- hashed with password_hash(). Register one account, then in phpMyAdmin
-- change its role to 'admin' to get an administrator account:
--   UPDATE users SET role='admin' WHERE username='yourusername';
