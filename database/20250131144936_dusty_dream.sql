CREATE DATABASE IF NOT EXISTS complaint_system;
USE complaint_system;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    email VARCHAR(100) NOT NULL
);

CREATE TABLE complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('complaint', 'appreciation') NOT NULL,
    status ENUM('open', 'in_progress', 'resolved') DEFAULT 'open',
    anonymous BOOLEAN DEFAULT FALSE,
    response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role, email) 
VALUES ('admin', '$2y$10$8KzQ8IzAF9tXXXXXXXXXXeYyXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'admin', 'admin@college.edu');