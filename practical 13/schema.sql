CREATE DATABASE IF NOT EXISTS student_api_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_api_db;

CREATE TABLE IF NOT EXISTS students (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  course     VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, course) VALUES
  ('Aarav Patel', 'aarav@example.com', 'B.Tech IT'),
  ('Diya Shah',   'diya@example.com',  'B.Tech CE');