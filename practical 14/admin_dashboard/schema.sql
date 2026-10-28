-- schema.sql : Admin Dashboard practical
CREATE DATABASE IF NOT EXISTS admin_dashboard CHARACTER SET utf8mb4;
USE admin_dashboard;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
  id   TINYINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE users (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id       TINYINT NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE students (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  enrollment_no VARCHAR(20) NOT NULL UNIQUE,
  name          VARCHAR(100) NOT NULL,
  department    VARCHAR(60) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  title      VARCHAR(150) NOT NULL,
  venue      VARCHAR(100) NOT NULL,
  event_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE registrations (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  student_id    INT NOT NULL,
  event_id      INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id)   REFERENCES events(id)   ON DELETE CASCADE
);

CREATE TABLE audit_logs (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  user_id    INT NULL,
  action     VARCHAR(50) NOT NULL,
  details    VARCHAR(255) NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------- Seed data ----------
INSERT INTO roles (name) VALUES ('admin'), ('faculty'), ('student');

-- All three demo users have the password:  password
INSERT INTO users (name, email, password_hash, role_id) VALUES
('Admin User',   'admin@demo.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Faculty User', 'faculty@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Student User', 'student@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

INSERT INTO students (enrollment_no, name, department) VALUES
('23IT001', 'Aarav Patel',  'IT'),
('23IT002', 'Diya Shah',    'IT'),
('23CE010', 'Rohan Mehta',  'CE'),
('23CS021', 'Isha Desai',   'CSE'),
('23IT033', 'Kunal Joshi',  'IT');

INSERT INTO events (title, venue, event_date) VALUES
('Hackathon 2026',       'Main Auditorium', '2026-11-10'),
('Web Dev Workshop',     'Lab 301',         '2026-11-18'),
('Tech Fest Quiz',       'Seminar Hall',    '2026-12-02');

INSERT INTO registrations (student_id, event_id, registered_at) VALUES
(1, 1, NOW() - INTERVAL 5 DAY),
(2, 1, NOW() - INTERVAL 4 DAY),
(3, 2, NOW() - INTERVAL 3 DAY),
(4, 2, NOW() - INTERVAL 2 DAY),
(5, 3, NOW() - INTERVAL 1 DAY),
(1, 3, NOW());
