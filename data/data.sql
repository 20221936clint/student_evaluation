-- Faculty Evaluation System Database

-- Create database
CREATE DATABASE IF NOT EXISTS checkmate;
USE checkmate;

-- Drop existing tables if they exist (for fresh install)
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS program_heads;
DROP TABLE IF EXISTS instructors;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS evaluations;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Program Heads Table
CREATE TABLE IF NOT EXISTS program_heads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    position VARCHAR(100) DEFAULT 'Program Head',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Instructors Table
CREATE TABLE IF NOT EXISTS instructors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    employee_id VARCHAR(50),
    position VARCHAR(100) DEFAULT 'Instructor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) NOT NULL,
    course_name VARCHAR(200) NOT NULL,
    description TEXT,
    department VARCHAR(100),
    instructor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Evaluations Table
CREATE TABLE IF NOT EXISTS evaluations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    instructor_id INT NOT NULL,
    course_id INT,
    rating DECIMAL(3,2),
    feedback TEXT,
    student_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Users (Password: password123)
-- Admin
INSERT INTO admins (first_name, last_name, email, password, role) VALUES
('System', 'Administrator', 'admin@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Program Heads
INSERT INTO program_heads (first_name, last_name, email, password, department, position) VALUES
('John', 'Head', 'head@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operational Management', 'Program Head'),
('Sarah', 'Manager', 'manager@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Management', 'Program Head'),
('Robert', 'Marketing', 'marketing@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marketing Management', 'Program Head');

-- Instructors
INSERT INTO instructors (first_name, last_name, email, password, department, employee_id, position) VALUES
('Jane', 'Smith', 'jane.smith@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operational Management', 'EMP0001', 'Instructor'),
('Michael', 'Brown', 'michael.brown@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Management', 'EMP0002', 'Instructor'),
('Sarah', 'Johnson', 'sarah.johnson@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marketing Management', 'EMP0003', 'Instructor'),
('David', 'Lee', 'david.lee@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operational Management', 'EMP0004', 'Instructor'),
('Emily', 'Davis', 'emily.davis@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Financial Management', 'EMP0005', 'Instructor'),
('James', 'Wilson', 'james.wilson@cjcm.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marketing Management', 'EMP0006', 'Instructor');

-- Courses
INSERT INTO courses (course_code, course_name, description, department) VALUES
('OM101', 'Operations Management 101', 'Introduction to operations management', 'Operational Management'),
('OM201', 'Operations Management 201', 'Advanced operations management', 'Operational Management'),
('OM301', 'Supply Chain Management', 'Supply chain strategies and management', 'Operational Management'),
('FM101', 'Financial Management 101', 'Introduction to financial management', 'Financial Management'),
('FM201', 'Financial Management 201', 'Corporate finance', 'Financial Management'),
('FM301', 'Investment Analysis', 'Investment strategies and analysis', 'Financial Management'),
('MM101', 'Marketing Management 101', 'Introduction to marketing', 'Marketing Management'),
('MM201', 'Marketing Management 201', 'Digital marketing', 'Marketing Management'),
('MM301', 'Consumer Behavior', 'Understanding consumer behavior', 'Marketing Management');

-- Sample Evaluations
INSERT INTO evaluations (instructor_id, course_id, rating, feedback, student_name) VALUES
(1, 1, 4.8, 'Excellent teaching methodology!', 'Student A'),
(1, 2, 4.6, 'Very knowledgeable and helpful.', 'Student B'),
(2, 4, 4.5, 'Great examples and case studies.', 'Student C'),
(2, 5, 4.7, 'Excellent financial concepts explained well.', 'Student D'),
(3, 7, 4.9, 'Loved the interactive sessions!', 'Student E'),
(3, 8, 4.4, 'Very engaging marketing strategies.', 'Student F');
