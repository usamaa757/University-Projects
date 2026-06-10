CREATE DATABASE IF NOT EXISTS academic_portal;
USE academic_portal;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    user_type ENUM('student', 'faculty', 'admin') DEFAULT 'student',
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Files table
CREATE TABLE files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    file_type ENUM('handout', 'midterm', 'final', 'software', 'project') NOT NULL,
    description TEXT,
    uploaded_by INT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    download_count INT DEFAULT 0,
    file_size BIGINT,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Feedback table
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Portfolio table
CREATE TABLE portfolio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    bio TEXT,
    skills TEXT,
    education TEXT,
    experience TEXT,
    projects TEXT,
    contact_email VARCHAR(100),
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);