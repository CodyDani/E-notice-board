CREATE DATABASE IF NOT EXISTS miracle_notice_board;
USE miracle_notice_board;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'lecturer', 'admin') NOT NULL DEFAULT 'student',
    identifier VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    target_role ENUM('student', 'lecturer', 'admin', 'all') NOT NULL DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed data for testing
INSERT INTO users (full_name, email, password_hash, role, identifier) VALUES
('System Administrator', 'admin@example.com', '$2y$10$PaV/hOsPEGhC6pz/4udkbuSBDmb9AJU2/zCsi7SwYNKRaEZo92dbC', 'admin', 'admin');

INSERT INTO notices (title, content, category, target_role) VALUES
('Welcome Students', 'Welcome to the new semester. Please check your timetable.', 'announcement', 'student'),
('Lecturer Meeting', 'There is a staff meeting on Friday at 2 PM.', 'meeting', 'lecturer'),
('System Maintenance', 'The system will be down for maintenance on Saturday.', 'maintenance', 'all'),
('Admin Update', 'New admin tools have been deployed.', 'update', 'admin');