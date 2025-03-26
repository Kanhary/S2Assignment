-- Create database
CREATE DATABASE IF NOT EXISTS assignment_system;
USE assignment_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Class enrollments table
CREATE TABLE IF NOT EXISTS class_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (class_id, student_id)
);

-- Assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    due_date DATETIME NOT NULL,
    class_id INT NOT NULL,
    teacher_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Submissions table
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    content TEXT NOT NULL,
    file_name VARCHAR(255),
    grade INT,
    feedback TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    graded_at DATETIME,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    UNIQUE KEY (student_id, assignment_id)
);

-- Insert sample data
-- Sample teachers
INSERT INTO users (name, email, password, role) VALUES
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher'),
('Jane Doe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher');

-- Sample students
INSERT INTO users (name, email, password, role) VALUES
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Bob Williams', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Charlie Brown', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Sample classes
INSERT INTO classes (name, description, teacher_id) VALUES
('Mathematics 101', 'Introduction to basic mathematics concepts', 1),
('Physics 101', 'Introduction to physics principles', 2),
('Computer Science 101', 'Introduction to programming and computer science', 1);

-- Sample enrollments
INSERT INTO class_enrollments (class_id, student_id) VALUES
(1, 3), (1, 4), (1, 5), -- All students in Math
(2, 3), (2, 5),         -- Alice and Charlie in Physics
(3, 4), (3, 5);         -- Bob and Charlie in CS

-- Sample assignments
INSERT INTO assignments (title, description, due_date, class_id, teacher_id) VALUES
('Algebra Basics', 'Complete problems 1-10 in Chapter 3', DATE_ADD(NOW(), INTERVAL 7 DAY), 1, 1),
('Newton\'s Laws', 'Write a 500-word essay on Newton\'s three laws of motion', DATE_ADD(NOW(), INTERVAL 14 DAY), 2, 2),
('Hello World Program', 'Write a simple program that prints "Hello, World!" in a language of your choice', DATE_ADD(NOW(), INTERVAL 5 DAY), 3, 1);

-- Sample submissions
INSERT INTO submissions (student_id, assignment_id, content, submitted_at) VALUES
(3, 1, 'Here are my solutions to the algebra problems...', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 1, 'I\'ve completed all the problems as requested...', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 2, 'My essay on Newton\'s Laws of Motion...', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Update some submissions with grades
UPDATE submissions SET grade = 85, feedback = 'Good work, but could improve on problem 7.', graded_at = NOW() WHERE student_id = 3 AND assignment_id = 1;
UPDATE submissions SET grade = 92, feedback = 'Excellent work!', graded_at = NOW() WHERE student_id = 4 AND assignment_id = 1;

