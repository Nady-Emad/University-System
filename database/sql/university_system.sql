CREATE DATABASE IF NOT EXISTS university_system
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE university_system;

SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS doctor_dashboard_summary;
DROP VIEW IF EXISTS student_gpa_summary;
DROP VIEW IF EXISTS doctor_exam_results;
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS exams;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student', 'doctor') NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    entry_year YEAR NOT NULL,
    student_code VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('active', 'inactive', 'graduated', 'suspended') NOT NULL DEFAULT 'active',
    current_gpa DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    current_cgpa DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    total_completed_credit_hours INT NOT NULL DEFAULT 0,
    total_quality_points DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE doctors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    specialization VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_doctors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE exams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    exam_type ENUM('midterm', 'final') NOT NULL,
    exam_date DATE NOT NULL,
    total_marks INT NOT NULL DEFAULT 100,
    credit_hours INT NOT NULL DEFAULT 3,
    semester ENUM('Fall', 'Spring', 'Summer') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL,
    credit_hours INT NOT NULL,
    quality_points DECIMAL(8,2) NOT NULL,
    semester ENUM('Fall', 'Spring', 'Summer') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_results_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_results_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    CONSTRAINT uq_student_exam UNIQUE (student_id, exam_id)
);

INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@university.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'admin'),

('Dr. Mohamed Ali', 'm.ali@university.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'doctor'),
('Dr. Reem Mostafa', 'r.mostafa@university.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'doctor'),
('Dr. Karim Fathy', 'k.fathy@university.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'doctor'),

('Ahmed Hassan', 'ahmed.hassan@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Mona Adel', 'mona.adel@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Youssef Samir', 'youssef.samir@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Sara Nabil', 'sara.nabil@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Omar Khaled', 'omar.khaled@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Nour Hany', 'nour.hany@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student');

INSERT INTO doctors (user_id, full_name, email, phone, specialization) VALUES
(2, 'Dr. Mohamed Ali', 'm.ali@university.com', '01211111111', 'Computer Science'),
(3, 'Dr. Reem Mostafa', 'r.mostafa@university.com', '01222222222', 'Information Systems'),
(4, 'Dr. Karim Fathy', 'k.fathy@university.com', '01233333333', 'Networks and Cyber Security');

INSERT INTO students (
    user_id, full_name, email, phone, entry_year, student_code, status,
    current_gpa, current_cgpa, total_completed_credit_hours, total_quality_points
) VALUES
(5, 'Ahmed Hassan', 'ahmed.hassan@student.com', '01012345678', 2024, '2024-0001', 'active', 0.00, 0.00, 0, 0.00),
(6, 'Mona Adel', 'mona.adel@student.com', '01022345678', 2024, '2024-0002', 'active', 0.00, 0.00, 0, 0.00),
(7, 'Youssef Samir', 'youssef.samir@student.com', '01032345678', 2025, '2025-0001', 'active', 0.00, 0.00, 0, 0.00),
(8, 'Sara Nabil', 'sara.nabil@student.com', '01042345678', 2025, '2025-0002', 'active', 0.00, 0.00, 0, 0.00),
(9, 'Omar Khaled', 'omar.khaled@student.com', '01052345678', 2023, '2023-0007', 'active', 0.00, 0.00, 0, 0.00),
(10, 'Nour Hany', 'nour.hany@student.com', '01062345678', 2023, '2023-0008', 'active', 0.00, 0.00, 0, 0.00);

INSERT INTO exams (
    title, subject_name, exam_type, exam_date, total_marks, credit_hours, semester, academic_year, doctor_id
) VALUES
('Programming 1 Midterm', 'Programming 1', 'midterm', '2026-03-15', 100, 3, 'Spring', '2025/2026', 1),
('Programming 1 Final', 'Programming 1', 'final', '2026-05-20', 100, 3, 'Spring', '2025/2026', 1),
('Database Systems Midterm', 'Database Systems', 'midterm', '2026-03-18', 100, 3, 'Spring', '2025/2026', 2),
('Database Systems Final', 'Database Systems', 'final', '2026-05-24', 100, 3, 'Spring', '2025/2026', 2),
('Computer Networks Midterm', 'Computer Networks', 'midterm', '2026-03-22', 100, 3, 'Spring', '2025/2026', 3),
('Computer Networks Final', 'Computer Networks', 'final', '2026-05-28', 100, 3, 'Spring', '2025/2026', 3),
('Web Development Midterm', 'Web Development', 'midterm', '2026-03-25', 100, 2, 'Spring', '2025/2026', 1),
('Web Development Final', 'Web Development', 'final', '2026-05-30', 100, 2, 'Spring', '2025/2026', 1),
('Cyber Security Basics Midterm', 'Cyber Security Basics', 'midterm', '2026-04-02', 100, 2, 'Spring', '2025/2026', 3),
('Cyber Security Basics Final', 'Cyber Security Basics', 'final', '2026-06-05', 100, 2, 'Spring', '2025/2026', 3);

CREATE OR REPLACE VIEW student_gpa_summary AS
SELECT
    s.id AS student_id,
    s.student_code,
    s.full_name,
    s.entry_year,
    s.status,
    ROUND(SUM(r.quality_points) / NULLIF(SUM(r.credit_hours), 0), 2) AS gpa,
    ROUND(SUM(r.quality_points) / NULLIF(SUM(r.credit_hours), 0), 2) AS cgpa,
    SUM(r.credit_hours) AS total_credit_hours,
    SUM(r.quality_points) AS total_quality_points
FROM students s
LEFT JOIN results r ON s.id = r.student_id
GROUP BY s.id, s.student_code, s.full_name, s.entry_year, s.status;

CREATE OR REPLACE VIEW doctor_exam_results AS
SELECT
    d.id AS doctor_id,
    d.full_name AS doctor_name,
    e.id AS exam_id,
    e.title AS exam_title,
    e.subject_name,
    e.exam_type,
    e.exam_date,
    e.semester,
    e.academic_year,
    s.id AS student_id,
    s.student_code,
    s.full_name AS student_name,
    r.marks,
    r.grade_point,
    r.credit_hours,
    r.quality_points
FROM doctors d
JOIN exams e ON d.id = e.doctor_id
LEFT JOIN results r ON e.id = r.exam_id
LEFT JOIN students s ON r.student_id = s.id;

CREATE OR REPLACE VIEW doctor_dashboard_summary AS
SELECT
    d.id AS doctor_id,
    d.full_name AS doctor_name,
    COUNT(DISTINCT e.id) AS total_assigned_exams,
    COUNT(DISTINCT CASE WHEN e.exam_type = 'midterm' THEN e.id END) AS total_midterm_exams,
    COUNT(DISTINCT CASE WHEN e.exam_type = 'final' THEN e.id END) AS total_final_exams,
    COUNT(DISTINCT r.id) AS total_entered_results,
    COUNT(DISTINCT r.student_id) AS total_students_in_exams
FROM doctors d
LEFT JOIN exams e ON d.id = e.doctor_id
LEFT JOIN results r ON e.id = r.exam_id
GROUP BY d.id, d.full_name;
