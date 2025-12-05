CREATE DATABASE IF NOT EXISTS campus_tailwind CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campus_tailwind;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','staff','visitor') DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(20) NOT NULL,
  description TEXT
);

CREATE TABLE labs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  department_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  room_no VARCHAR(50),
  description TEXT,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);



