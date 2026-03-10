CREATE DATABASE IF NOT EXISTS university_system
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE university_system;

SET FOREIGN_KEY_CHECKS = 0;

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
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
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
    current_gpa DECIMAL(3,2) DEFAULT 0.00,
    current_cgpa DECIMAL(3,2) DEFAULT 0.00,
    total_completed_credit_hours INT NOT NULL DEFAULT 0,
    total_quality_points DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE doctors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    specialization VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE exams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    total_marks INT NOT NULL,
    credit_hours INT NOT NULL DEFAULT 3,
    semester ENUM('Fall', 'Spring', 'Summer') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    doctor_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL
);

CREATE TABLE results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL,
    credit_hours INT NOT NULL,
    quality_points DECIMAL(6,2) NOT NULL,
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
('Ahmed Hassan', 'ahmed.hassan@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Mona Adel', 'mona.adel@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Youssef Samir', 'youssef.samir@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Sara Nabil', 'sara.nabil@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student');

INSERT INTO students (
    user_id, full_name, email, phone, entry_year, student_code, status,
    current_gpa, current_cgpa, total_completed_credit_hours, total_quality_points
) VALUES
(2, 'Ahmed Hassan', 'ahmed.hassan@student.com', '01012345678', 2024, '2024-0001', 'active', 0.00, 0.00, 0, 0.00),
(3, 'Mona Adel', 'mona.adel@student.com', '01022345678', 2024, '2024-0002', 'active', 0.00, 0.00, 0, 0.00),
(4, 'Youssef Samir', 'youssef.samir@student.com', '01032345678', 2025, '2025-0001', 'active', 0.00, 0.00, 0, 0.00),
(5, 'Sara Nabil', 'sara.nabil@student.com', '01042345678', 2025, '2025-0002', 'active', 0.00, 0.00, 0, 0.00);

INSERT INTO doctors (full_name, email, phone, specialization) VALUES
('Dr. Mohamed Ali', 'm.ali@university.com', '01211111111', 'Computer Science'),
('Dr. Reem Mostafa', 'r.mostafa@university.com', '01222222222', 'Information Systems'),
('Dr. Karim Fathy', 'k.fathy@university.com', '01233333333', 'Networks and Security');

INSERT INTO exams (title, subject_name, exam_date, total_marks, credit_hours, semester, academic_year, doctor_id) VALUES
('Midterm Exam', 'Programming 1', '2026-03-20', 100, 3, 'Spring', '2025/2026', 1),
('Final Exam', 'Database Systems', '2026-05-25', 100, 3, 'Spring', '2025/2026', 2),
('Midterm Exam', 'Computer Networks', '2026-03-28', 100, 3, 'Spring', '2025/2026', 3),
('Final Exam', 'Web Development', '2026-05-30', 100, 2, 'Spring', '2025/2026', 1),
('Final Exam', 'Operating Systems', '2026-06-02', 100, 3, 'Spring', '2025/2026', 1),
('Quiz Exam', 'Cyber Security Basics', '2026-04-10', 100, 2, 'Spring', '2025/2026', 3);

INSERT INTO results (
    student_id, exam_id, marks, grade_point, credit_hours, quality_points, semester, academic_year
) VALUES
(1, 1, 91.00, 4.00, 3, 12.00, 'Spring', '2025/2026'),
(1, 2, 84.00, 3.30, 3, 9.90, 'Spring', '2025/2026'),
(1, 3, 77.00, 3.00, 3, 9.00, 'Spring', '2025/2026'),
(1, 4, 88.00, 3.70, 2, 7.40, 'Spring', '2025/2026'),

(2, 1, 72.00, 2.70, 3, 8.10, 'Spring', '2025/2026'),
(2, 2, 69.00, 2.30, 3, 6.90, 'Spring', '2025/2026'),
(2, 3, 81.00, 3.30, 3, 9.90, 'Spring', '2025/2026'),
(2, 6, 95.00, 4.00, 2, 8.00, 'Spring', '2025/2026'),

(3, 1, 65.00, 2.30, 3, 6.90, 'Spring', '2025/2026'),
(3, 4, 74.00, 2.70, 2, 5.40, 'Spring', '2025/2026'),
(3, 5, 58.00, 0.00, 3, 0.00, 'Spring', '2025/2026'),
(3, 6, 79.00, 3.00, 2, 6.00, 'Spring', '2025/2026'),

(4, 2, 87.00, 3.70, 3, 11.10, 'Spring', '2025/2026'),
(4, 3, 90.00, 4.00, 3, 12.00, 'Spring', '2025/2026'),
(4, 4, 83.00, 3.30, 2, 6.60, 'Spring', '2025/2026'),
(4, 5, 76.00, 3.00, 3, 9.00, 'Spring', '2025/2026');

UPDATE students s
JOIN (
    SELECT
        student_id,
        SUM(credit_hours) AS total_ch,
        SUM(quality_points) AS total_qp,
        ROUND(SUM(quality_points) / NULLIF(SUM(credit_hours), 0), 2) AS calculated_cgpa
    FROM results
    GROUP BY student_id
) r ON s.id = r.student_id
SET
    s.total_completed_credit_hours = r.total_ch,
    s.total_quality_points = r.total_qp,
    s.current_gpa = r.calculated_cgpa,
    s.current_cgpa = r.calculated_cgpa;

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

CREATE OR REPLACE VIEW student_result_details AS
SELECT
    r.id,
    s.student_code,
    s.full_name AS student_name,
    e.subject_name,
    e.title AS exam_title,
    e.exam_date,
    r.marks,
    r.grade_point,
    r.credit_hours,
    r.quality_points,
    r.semester,
    r.academic_year
FROM results r
JOIN students s ON r.student_id = s.id
JOIN exams e ON r.exam_id = e.id;

-- Optional test queries

-- 1) Show all students
-- SELECT * FROM students;

-- 2) Show all result details
-- SELECT * FROM student_result_details ORDER BY student_name, exam_date;

-- 3) Show GPA / CGPA summary
-- SELECT * FROM student_gpa_summary ORDER BY cgpa DESC;

-- 4) Admin login
-- email: admin@university.com
-- password: password

-- 5) Student login examples
-- ahmed.hassan@student.com / password
-- mona.adel@student.com / password
-- youssef.samir@student.com / password
-- sara.nabil@student.com / password

CREATE DATABASE IF NOT EXISTS university_system
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE university_system;

SET FOREIGN_KEY_CHECKS = 0;

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
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
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
    current_gpa DECIMAL(3,2) DEFAULT 0.00,
    current_cgpa DECIMAL(3,2) DEFAULT 0.00,
    total_completed_credit_hours INT NOT NULL DEFAULT 0,
    total_quality_points DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE doctors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    specialization VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE exams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    total_marks INT NOT NULL,
    credit_hours INT NOT NULL DEFAULT 3,
    semester ENUM('Fall', 'Spring', 'Summer') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    doctor_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL
);

CREATE TABLE results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL,
    credit_hours INT NOT NULL,
    quality_points DECIMAL(6,2) NOT NULL,
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
('Ahmed Hassan', 'ahmed.hassan@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Mona Adel', 'mona.adel@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Youssef Samir', 'youssef.samir@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student'),
('Sara Nabil', 'sara.nabil@student.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9LlEdsW5M4rE8p6r1Qe6wO', 'student');

INSERT INTO students (
    user_id, full_name, email, phone, entry_year, student_code, status,
    current_gpa, current_cgpa, total_completed_credit_hours, total_quality_points
) VALUES
(2, 'Ahmed Hassan', 'ahmed.hassan@student.com', '01012345678', 2024, '2024-0001', 'active', 0.00, 0.00, 0, 0.00),
(3, 'Mona Adel', 'mona.adel@student.com', '01022345678', 2024, '2024-0002', 'active', 0.00, 0.00, 0, 0.00),
(4, 'Youssef Samir', 'youssef.samir@student.com', '01032345678', 2025, '2025-0001', 'active', 0.00, 0.00, 0, 0.00),
(5, 'Sara Nabil', 'sara.nabil@student.com', '01042345678', 2025, '2025-0002', 'active', 0.00, 0.00, 0, 0.00);

INSERT INTO doctors (full_name, email, phone, specialization) VALUES
('Dr. Mohamed Ali', 'm.ali@university.com', '01211111111', 'Computer Science'),
('Dr. Reem Mostafa', 'r.mostafa@university.com', '01222222222', 'Information Systems'),
('Dr. Karim Fathy', 'k.fathy@university.com', '01233333333', 'Networks and Security');

INSERT INTO exams (title, subject_name, exam_date, total_marks, credit_hours, semester, academic_year, doctor_id) VALUES
('Midterm Exam', 'Programming 1', '2026-03-20', 100, 3, 'Spring', '2025/2026', 1),
('Final Exam', 'Database Systems', '2026-05-25', 100, 3, 'Spring', '2025/2026', 2),
('Midterm Exam', 'Computer Networks', '2026-03-28', 100, 3, 'Spring', '2025/2026', 3),
('Final Exam', 'Web Development', '2026-05-30', 100, 2, 'Spring', '2025/2026', 1),
('Final Exam', 'Operating Systems', '2026-06-02', 100, 3, 'Spring', '2025/2026', 1),
('Quiz Exam', 'Cyber Security Basics', '2026-04-10', 100, 2, 'Spring', '2025/2026', 3);

INSERT INTO results (
    student_id, exam_id, marks, grade_point, credit_hours, quality_points, semester, academic_year
) VALUES
(1, 1, 91.00, 4.00, 3, 12.00, 'Spring', '2025/2026'),
(1, 2, 84.00, 3.30, 3, 9.90, 'Spring', '2025/2026'),
(1, 3, 77.00, 3.00, 3, 9.00, 'Spring', '2025/2026'),
(1, 4, 88.00, 3.70, 2, 7.40, 'Spring', '2025/2026'),

(2, 1, 72.00, 2.70, 3, 8.10, 'Spring', '2025/2026'),
(2, 2, 69.00, 2.30, 3, 6.90, 'Spring', '2025/2026'),
(2, 3, 81.00, 3.30, 3, 9.90, 'Spring', '2025/2026'),
(2, 6, 95.00, 4.00, 2, 8.00, 'Spring', '2025/2026'),

(3, 1, 65.00, 2.30, 3, 6.90, 'Spring', '2025/2026'),
(3, 4, 74.00, 2.70, 2, 5.40, 'Spring', '2025/2026'),
(3, 5, 58.00, 0.00, 3, 0.00, 'Spring', '2025/2026'),
(3, 6, 79.00, 3.00, 2, 6.00, 'Spring', '2025/2026'),

(4, 2, 87.00, 3.70, 3, 11.10, 'Spring', '2025/2026'),
(4, 3, 90.00, 4.00, 3, 12.00, 'Spring', '2025/2026'),
(4, 4, 83.00, 3.30, 2, 6.60, 'Spring', '2025/2026'),
(4, 5, 76.00, 3.00, 3, 9.00, 'Spring', '2025/2026');

UPDATE students s
JOIN (
    SELECT
        student_id,
        SUM(credit_hours) AS total_ch,
        SUM(quality_points) AS total_qp,
        ROUND(SUM(quality_points) / NULLIF(SUM(credit_hours), 0), 2) AS calculated_cgpa
    FROM results
    GROUP BY student_id
) r ON s.id = r.student_id
SET
    s.total_completed_credit_hours = r.total_ch,
    s.total_quality_points = r.total_qp,
    s.current_gpa = r.calculated_cgpa,
    s.current_cgpa = r.calculated_cgpa;

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

CREATE OR REPLACE VIEW student_result_details AS
SELECT
    r.id,
    s.student_code,
    s.full_name AS student_name,
    e.subject_name,
    e.title AS exam_title,
    e.exam_date,
    r.marks,
    r.grade_point,
    r.credit_hours,
    r.quality_points,
    r.semester,
    r.academic_year
FROM results r
JOIN students s ON r.student_id = s.id
JOIN exams e ON r.exam_id = e.id;

-- Optional test queries

-- 1) Show all students
-- SELECT * FROM students;

-- 2) Show all result details
-- SELECT * FROM student_result_details ORDER BY student_name, exam_date;

-- 3) Show GPA / CGPA summary
-- SELECT * FROM student_gpa_summary ORDER BY cgpa DESC;

-- 4) Admin login
-- email: admin@university.com
-- password: password

-- 5) Student login examples
-- ahmed.hassan@student.com / password
-- mona.adel@student.com / password
-- youssef.samir@student.com / password
-- sara.nabil@student.com / password

USE university_system;

SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS exam_attempt_summary;

DROP TABLE IF EXISTS student_answers;
DROP TABLE IF EXISTS student_exam_attempts;
DROP TABLE IF EXISTS question_choices;
DROP TABLE IF EXISTS exam_questions;
DROP TABLE IF EXISTS student_subject;
DROP TABLE IF EXISTS doctor_subject;
DROP TABLE IF EXISTS exams_new;
DROP TABLE IF EXISTS subjects;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- 1) SUBJECTS
-- =========================================
CREATE TABLE subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    credit_hours INT NOT NULL DEFAULT 3,
    semester ENUM('Fall', 'Spring', 'Summer') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================
-- 2) DOCTOR <-> SUBJECT
-- =========================================
CREATE TABLE doctor_subject (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doctor_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_doctor_subject_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    CONSTRAINT fk_doctor_subject_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT uq_doctor_subject UNIQUE (doctor_id, subject_id)
);

-- =========================================
-- 3) STUDENT <-> SUBJECT
-- =========================================
CREATE TABLE student_subject (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    enrollment_status ENUM('enrolled', 'dropped', 'completed') NOT NULL DEFAULT 'enrolled',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_subject_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_subject_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT uq_student_subject UNIQUE (student_id, subject_id)
);

-- =========================================
-- 4) NEW EXAMS TABLE FOR REAL EXAM TAKING
-- =========================================
CREATE TABLE exams_new (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    exam_type ENUM('midterm', 'final', 'quiz', 'practical') NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    duration_minutes INT NOT NULL,
    total_marks DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'published', 'closed') NOT NULL DEFAULT 'draft',
    allow_retake TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_new_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT fk_exams_new_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- =========================================
-- 5) EXAM QUESTIONS
-- =========================================
CREATE TABLE exam_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id BIGINT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    mark DECIMAL(6,2) NOT NULL DEFAULT 1.00,
    order_no INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exam_questions_exam FOREIGN KEY (exam_id) REFERENCES exams_new(id) ON DELETE CASCADE
);

-- =========================================
-- 6) QUESTION CHOICES
-- =========================================
CREATE TABLE question_choices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    choice_text VARCHAR(500) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_question_choices_question FOREIGN KEY (question_id) REFERENCES exam_questions(id) ON DELETE CASCADE
);

-- =========================================
-- 7) STUDENT EXAM ATTEMPTS
-- =========================================
CREATE TABLE student_exam_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NOT NULL,
    started_at DATETIME NULL,
    submitted_at DATETIME NULL,
    status ENUM('in_progress', 'submitted', 'auto_submitted') NOT NULL DEFAULT 'in_progress',
    obtained_marks DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    total_marks DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    grade_point DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    quality_points DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_exam_attempts_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_exam_attempts_exam FOREIGN KEY (exam_id) REFERENCES exams_new(id) ON DELETE CASCADE
);

-- ملاحظة:
-- إذا allow_retake = 0 في التطبيق، امنع أكثر من محاولة من Laravel
-- لو تريد منعها من SQL بالكامل أقدر أعملها Trigger أو Unique مشروط بمنطق مختلف.

-- =========================================
-- 8) STUDENT ANSWERS
-- =========================================
CREATE TABLE student_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    selected_choice_id BIGINT UNSIGNED NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    obtained_mark DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_answers_attempt FOREIGN KEY (attempt_id) REFERENCES student_exam_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_answers_question FOREIGN KEY (question_id) REFERENCES exam_questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_answers_choice FOREIGN KEY (selected_choice_id) REFERENCES question_choices(id) ON DELETE SET NULL,
    CONSTRAINT uq_attempt_question UNIQUE (attempt_id, question_id)
);

-- =========================================
-- 9) SAMPLE SUBJECTS
-- =========================================
INSERT INTO subjects (code, name, description, credit_hours, semester, academic_year) VALUES
('CS101', 'Programming 1', 'Introduction to programming fundamentals using C++', 3, 'Spring', '2025/2026'),
('DB201', 'Database Systems', 'Relational database design and SQL', 3, 'Spring', '2025/2026'),
('NET301', 'Computer Networks', 'Network models, routing, switching, and protocols', 3, 'Spring', '2025/2026'),
('WEB205', 'Web Development', 'Frontend and backend web development basics', 2, 'Spring', '2025/2026'),
('SEC220', 'Cyber Security Basics', 'Fundamentals of information and cyber security', 2, 'Spring', '2025/2026');

-- =========================================
-- 10) LINK DOCTORS TO SUBJECTS
-- =========================================
INSERT INTO doctor_subject (doctor_id, subject_id) VALUES
(1, 1),
(1, 4),
(2, 2),
(3, 3),
(3, 5);

-- =========================================
-- 11) ENROLL STUDENTS IN SUBJECTS
-- =========================================
INSERT INTO student_subject (student_id, subject_id, enrollment_status) VALUES
(1, 1, 'enrolled'),
(1, 2, 'enrolled'),
(1, 3, 'enrolled'),
(1, 4, 'enrolled'),

(2, 1, 'enrolled'),
(2, 2, 'enrolled'),
(2, 4, 'enrolled'),

(3, 2, 'enrolled'),
(3, 3, 'enrolled'),
(3, 5, 'enrolled'),

(4, 1, 'enrolled'),
(4, 4, 'enrolled'),
(4, 5, 'enrolled'),

(5, 2, 'enrolled'),
(5, 3, 'enrolled'),
(5, 5, 'enrolled'),

(6, 1, 'enrolled'),
(6, 2, 'enrolled'),
(6, 3, 'enrolled');

-- =========================================
-- 12) REAL EXAMS
-- =========================================
INSERT INTO exams_new (
    title, exam_type, subject_id, doctor_id, start_time, end_time, duration_minutes, total_marks, status, allow_retake
) VALUES
('Programming 1 Midterm Exam', 'midterm', 1, 1, '2026-03-20 10:00:00', '2026-03-20 12:00:00', 60, 10.00, 'published', 0),
('Database Systems Final Exam', 'final', 2, 2, '2026-05-24 09:00:00', '2026-05-24 12:00:00', 90, 10.00, 'published', 0),
('Computer Networks Midterm Exam', 'midterm', 3, 3, '2026-03-25 11:00:00', '2026-03-25 13:00:00', 60, 10.00, 'published', 0),
('Cyber Security Basics Quiz', 'quiz', 5, 3, '2026-04-10 10:00:00', '2026-04-10 11:00:00', 30, 5.00, 'published', 1);

-- =========================================
-- 13) QUESTIONS FOR EXAM 1
-- =========================================
INSERT INTO exam_questions (exam_id, question_text, mark, order_no) VALUES
(1, 'Which of the following is a valid C++ data type?', 2.00, 1),
(1, 'Which loop is guaranteed to execute at least once?', 2.00, 2),
(1, 'What is the correct symbol used to terminate a C++ statement?', 2.00, 3),
(1, 'Which keyword is used to define a constant in C++?', 2.00, 4),
(1, 'Which operator is used for equality comparison?', 2.00, 5);

INSERT INTO question_choices (question_id, choice_text, is_correct) VALUES
(1, 'integer', 0),
(1, 'int', 1),
(1, 'number', 0),
(1, 'real', 0),

(2, 'for', 0),
(2, 'while', 0),
(2, 'do while', 1),
(2, 'foreach', 0),

(3, '.', 0),
(3, ':', 0),
(3, ';', 1),
(3, ',', 0),

(4, 'const', 1),
(4, 'static', 0),
(4, 'fixed', 0),
(4, 'definevar', 0),

(5, '=', 0),
(5, '==', 1),
(5, '!=', 0),
(5, '===', 0);

-- =========================================
-- 14) QUESTIONS FOR EXAM 2
-- =========================================
INSERT INTO exam_questions (exam_id, question_text, mark, order_no) VALUES
(2, 'Which SQL command is used to retrieve data?', 2.00, 1),
(2, 'Which normal form removes partial dependency?', 2.00, 2),
(2, 'Which key uniquely identifies a record?', 2.00, 3),
(2, 'Which JOIN returns matching rows from both tables?', 2.00, 4),
(2, 'Which statement is used to remove a table?', 2.00, 5);

INSERT INTO question_choices (question_id, choice_text, is_correct) VALUES
(6, 'SELECT', 1),
(6, 'INSERT', 0),
(6, 'UPDATE', 0),
(6, 'DELETE', 0),

(7, '1NF', 0),
(7, '2NF', 1),
(7, '3NF', 0),
(7, 'BCNF', 0),

(8, 'Foreign Key', 0),
(8, 'Candidate Key', 0),
(8, 'Primary Key', 1),
(8, 'Composite Key', 0),

(9, 'LEFT JOIN', 0),
(9, 'RIGHT JOIN', 0),
(9, 'INNER JOIN', 1),
(9, 'CROSS JOIN', 0),

(10, 'DROP TABLE', 1),
(10, 'DELETE TABLE', 0),
(10, 'REMOVE TABLE', 0),
(10, 'TRUNCATE TABLE', 0);

-- =========================================
-- 15) QUESTIONS FOR EXAM 3
-- =========================================
INSERT INTO exam_questions (exam_id, question_text, mark, order_no) VALUES
(3, 'Which device forwards packets between networks?', 2.00, 1),
(3, 'Which protocol is used to assign IP addresses automatically?', 2.00, 2),
(3, 'What does LAN stand for?', 2.00, 3),
(3, 'Which layer handles routing in the OSI model?', 2.00, 4),
(3, 'Which cable type is commonly used in Ethernet networks?', 2.00, 5);

INSERT INTO question_choices (question_id, choice_text, is_correct) VALUES
(11, 'Switch', 0),
(11, 'Router', 1),
(11, 'Hub', 0),
(11, 'Repeater', 0),

(12, 'DNS', 0),
(12, 'DHCP', 1),
(12, 'FTP', 0),
(12, 'SMTP', 0),

(13, 'Local Area Network', 1),
(13, 'Long Access Node', 0),
(13, 'Line Area Network', 0),
(13, 'Linked Access Network', 0),

(14, 'Transport', 0),
(14, 'Network', 1),
(14, 'Session', 0),
(14, 'Application', 0),

(15, 'Fiber only', 0),
(15, 'Coaxial only', 0),
(15, 'Twisted Pair', 1),
(15, 'Serial cable', 0);

-- =========================================
-- 16) SAMPLE STUDENT ATTEMPT
-- الطالب 1 دخل الامتحان 1
-- =========================================
INSERT INTO student_exam_attempts (
    student_id, exam_id, started_at, submitted_at, status, obtained_marks, total_marks, percentage, grade_point, quality_points
) VALUES
(1, 1, '2026-03-20 10:05:00', '2026-03-20 10:40:00', 'submitted', 8.00, 10.00, 80.00, 3.30, 9.90);

-- =========================================
-- 17) SAMPLE STUDENT ANSWERS
-- attempt_id = 1
-- =========================================
INSERT INTO student_answers (attempt_id, question_id, selected_choice_id, is_correct, obtained_mark) VALUES
(1, 1, 2, 1, 2.00),
(1, 2, 7, 1, 2.00),
(1, 3, 11, 1, 2.00),
(1, 4, 13, 1, 2.00),
(1, 5, 17, 0, 0.00);

-- =========================================
-- 18) OPTIONAL: SYNC TO RESULTS TABLE
-- هذا يربط الامتحان الحقيقي بجدول النتائج الحالي
-- =========================================
INSERT INTO results (
    student_id, exam_id, marks, grade_point, credit_hours, quality_points, semester, academic_year
)
SELECT
    sea.student_id,
    1,
    sea.obtained_marks * 10,
    sea.grade_point,
    s.credit_hours,
    sea.grade_point * s.credit_hours,
    s.semester,
    s.academic_year
FROM student_exam_attempts sea
JOIN exams_new en ON en.id = sea.exam_id
JOIN subjects s ON s.id = en.subject_id
WHERE sea.id = 1
AND NOT EXISTS (
    SELECT 1
    FROM results r
    WHERE r.student_id = sea.student_id
      AND r.exam_id = 1
);

-- =========================================
-- 19) VIEW: EXAM ATTEMPT SUMMARY
-- =========================================
CREATE OR REPLACE VIEW exam_attempt_summary AS
SELECT
    sea.id AS attempt_id,
    st.student_code,
    st.full_name AS student_name,
    sub.code AS subject_code,
    sub.name AS subject_name,
    en.title AS exam_title,
    en.exam_type,
    d.full_name AS doctor_name,
    sea.started_at,
    sea.submitted_at,
    sea.status,
    sea.obtained_marks,
    sea.total_marks,
    sea.percentage,
    sea.grade_point,
    sea.quality_points
FROM student_exam_attempts sea
JOIN students st ON st.id = sea.student_id
JOIN exams_new en ON en.id = sea.exam_id
JOIN subjects sub ON sub.id = en.subject_id
JOIN doctors d ON d.id = en.doctor_id;

-- =========================================
-- 20) HELPFUL TEST QUERIES
-- =========================================

-- المواد
-- SELECT * FROM subjects;

-- المواد الخاصة بدكتور معين
-- SELECT d.full_name, s.code, s.name
-- FROM doctor_subject ds
-- JOIN doctors d ON d.id = ds.doctor_id
-- JOIN subjects s ON s.id = ds.subject_id
-- ORDER BY d.full_name, s.name;

-- المواد المسجل فيها الطالب
-- SELECT st.full_name, sub.code, sub.name
-- FROM student_subject ss
-- JOIN students st ON st.id = ss.student_id
-- JOIN subjects sub ON sub.id = ss.subject_id
-- ORDER BY st.full_name, sub.name;

-- الامتحانات المنشورة
-- SELECT en.*, sub.name AS subject_name, d.full_name AS doctor_name
-- FROM exams_new en
-- JOIN subjects sub ON sub.id = en.subject_id
-- JOIN doctors d ON d.id = en.doctor_id
-- WHERE en.status = 'published';

-- أسئلة امتحان 1
-- SELECT q.id, q.question_text, q.mark, c.id AS choice_id, c.choice_text, c.is_correct
-- FROM exam_questions q
-- JOIN question_choices c ON c.question_id = q.id
-- WHERE q.exam_id = 1
-- ORDER BY q.order_no, c.id;

-- نتائج المحاولات
-- SELECT * FROM exam_attempt_summary;

-- إجابات الطالب في محاولة 1
-- SELECT q.question_text, c.choice_text AS selected_choice, sa.is_correct, sa.obtained_mark
-- FROM student_answers sa
-- JOIN exam_questions q ON q.id = sa.question_id
-- LEFT JOIN question_choices c ON c.id = sa.selected_choice_id
-- WHERE sa.attempt_id = 1
-- ORDER BY q.order_no;