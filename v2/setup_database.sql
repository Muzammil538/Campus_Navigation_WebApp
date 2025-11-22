-- Create Database
CREATE DATABASE IF NOT EXISTS campus_navigator;
USE campus_navigator;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'staff', 'visitor') NOT NULL,
    student_id VARCHAR(50) NULL,
    department VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    profile_image VARCHAR(255) NULL,
    accessibility_mode BOOLEAN DEFAULT FALSE,
    voice_navigation BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buildings Table
CREATE TABLE buildings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    category ENUM('academic', 'library', 'laboratory', 'cafeteria', 'sports', 'administration', 'dormitory', 'parking') NOT NULL,
    description TEXT,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    address VARCHAR(255),
    floors INT DEFAULT 1,
    total_rooms INT DEFAULT 0,
    image_url VARCHAR(255),
    operating_hours JSON,
    accessibility_features TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Facilities Table
CREATE TABLE facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    building_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    floor INT,
    room_number VARCHAR(20),
    description TEXT,
    capacity INT,
    amenities JSON,
    accessibility_info TEXT,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    INDEX idx_building (building_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Favorites/Bookmarks Table
CREATE TABLE bookmarks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    building_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_bookmark (user_id, building_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Navigation History Table
CREATE TABLE navigation_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    from_location VARCHAR(100),
    to_building_id INT NOT NULL,
    distance DECIMAL(10, 2),
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feedback Table
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category ENUM('bug', 'feature', 'general') NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    screenshot_url VARCHAR(255),
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Emergency Contacts Table
CREATE TABLE emergency_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('security', 'medical', 'helpline', 'fire', 'admin') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    available_24_7 BOOLEAN DEFAULT TRUE,
    description TEXT,
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events Table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    building_id INT NOT NULL,
    room_number VARCHAR(20),
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    organizer VARCHAR(100),
    target_audience ENUM('all', 'students', 'staff') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    INDEX idx_date (start_time, end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Settings Table
CREATE TABLE user_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    theme ENUM('light', 'dark', 'auto') DEFAULT 'light',
    language VARCHAR(10) DEFAULT 'en',
    text_size ENUM('small', 'medium', 'large', 'xlarge') DEFAULT 'medium',
    high_contrast BOOLEAN DEFAULT FALSE,
    voice_speed DECIMAL(3, 1) DEFAULT 1.0,
    map_preference VARCHAR(50) DEFAULT 'standard',
    avoid_stairs BOOLEAN DEFAULT FALSE,
    notifications_enabled BOOLEAN DEFAULT TRUE,
    location_sharing BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Data
-- Insert Buildings (Sample Campus Data)
INSERT INTO buildings (name, code, category, description, latitude, longitude, address, floors, operating_hours) VALUES
('Engineering Complex A', 'ENG-A', 'academic', 'Main engineering building with labs and lecture halls', 28.544907, 77.192337, '123 Campus Way, Building 14', 4, '{"monday": "7:00-21:00", "tuesday": "7:00-21:00", "wednesday": "7:00-21:00", "thursday": "7:00-21:00", "friday": "7:00-21:00", "saturday": "9:00-18:00", "sunday": "Closed"}'),
('Central Library', 'LIB-01', 'library', 'Main university library with extensive collections', 28.545123, 77.192890, 'Library Square', 3, '{"monday": "8:00-22:00", "tuesday": "8:00-22:00", "wednesday": "8:00-22:00", "thursday": "8:00-22:00", "friday": "8:00-20:00", "saturday": "9:00-18:00", "sunday": "10:00-17:00"}'),
('Student Union', 'STU-01', 'cafeteria', 'Student center with dining and recreation', 28.544500, 77.193100, 'Student Plaza', 2, '{"monday": "6:00-22:00", "tuesday": "6:00-22:00", "wednesday": "6:00-22:00", "thursday": "6:00-22:00", "friday": "6:00-23:00", "saturday": "8:00-23:00", "sunday": "8:00-22:00"}'),
('Science Center', 'SCI-01', 'laboratory', 'Research labs and science facilities', 28.545890, 77.192100, 'Science Avenue', 5, '{"monday": "7:00-20:00", "tuesday": "7:00-20:00", "wednesday": "7:00-20:00", "thursday": "7:00-20:00", "friday": "7:00-20:00", "saturday": "9:00-17:00", "sunday": "Closed"}'),
('Recreation Center', 'REC-01', 'sports', 'Gym, pool, and sports facilities', 28.543800, 77.193500, 'Athletic Complex', 3, '{"monday": "6:00-22:00", "tuesday": "6:00-22:00", "wednesday": "6:00-22:00", "thursday": "6:00-22:00", "friday": "6:00-22:00", "saturday": "7:00-21:00", "sunday": "8:00-20:00"}'),
('Administration Building', 'ADM-01', 'administration', 'Main administrative offices', 28.545500, 77.191800, 'Admin Plaza', 4, '{"monday": "8:00-17:00", "tuesday": "8:00-17:00", "wednesday": "8:00-17:00", "thursday": "8:00-17:00", "friday": "8:00-17:00", "saturday": "Closed", "sunday": "Closed"}'),
('North Parking P3', 'PRK-03', 'parking', 'Multi-level parking structure', 28.546200, 77.192500, 'North Campus Drive', 3, '{"monday": "24/7", "tuesday": "24/7", "wednesday": "24/7", "thursday": "24/7", "friday": "24/7", "saturday": "24/7", "sunday": "24/7"}');

-- Insert Facilities
INSERT INTO facilities (building_id, name, type, floor, room_number, capacity) VALUES
(1, 'Materials Lab', 'laboratory', 2, '201', 30),
(1, 'Lecture Hall A1', 'classroom', 1, '101', 150),
(1, 'Computer Lab', 'laboratory', 3, '301', 40),
(1, 'Advising Office', 'office', 4, '402', 5),
(2, 'Reading Room', 'study', 2, '201', 100),
(2, 'Computer Area', 'study', 1, '105', 30),
(2, 'Group Study Rooms', 'study', 2, '210-220', 6),
(3, 'Main Cafeteria', 'dining', 1, 'Ground', 300),
(3, 'Coffee Shop', 'dining', 1, '102', 50),
(5, 'Main Gym', 'fitness', 1, 'Ground', 100),
(5, 'Swimming Pool', 'fitness', 2, '201', 50);

-- Insert Emergency Contacts
INSERT INTO emergency_contacts (name, type, phone, email, latitude, longitude, available_24_7) VALUES
('Campus Security', 'security', '555-123-4567', 'security@university.edu', 28.545000, 77.192500, TRUE),
('Medical Center', 'medical', '555-123-4568', 'health@university.edu', 28.544600, 77.193200, TRUE),
('Campus Helpline', 'helpline', '555-123-4569', 'help@university.edu', 28.545200, 77.192800, TRUE);
