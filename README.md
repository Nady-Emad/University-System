# University Management System

<p align="center">
	A modern Laravel platform to manage academic operations, online exams, and student performance in one unified system.
</p>

<p align="center">
	<img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel 12" />
	<img src="https://img.shields.io/badge/PHP-8.2%2B-777bb4" alt="PHP 8.2+" />
	<img src="https://img.shields.io/badge/Database-MySQL-00758f" alt="MySQL" />
	<img src="https://img.shields.io/badge/Build-Vite-646cff" alt="Vite" />
	<img src="https://img.shields.io/badge/License-MIT-green" alt="MIT License" />
</p>

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Role Capabilities](#role-capabilities)
- [Technology Stack](#technology-stack)
- [Architecture](#architecture)
- [Database Model](#database-model)
- [Quick Start](#quick-start)
- [Default Credentials](#default-credentials)
- [Screenshots](#screenshots)
- [Roadmap](#roadmap)
- [License](#license)

## Overview

University Management System helps institutions digitize core workflows including:

- User and role management
- Subject assignment and enrollment
- Online exam delivery and auto-grading
- GPA and CGPA tracking
- Performance dashboards for Admin, Doctor, and Student users

The platform is built with Laravel MVC architecture and designed to be scalable, maintainable, and role-secure.

## Key Features

| Area | Highlights |
| --- | --- |
| Access Control | Role-based authentication for Admin, Doctor, and Student |
| Academic Management | Student, doctor, subject, and enrollment management |
| Exam Engine | Midterm/final exams, MCQ question bank, online attempts |
| Grading | Automatic grading with attempt tracking |
| Analytics | Dashboards and result/statistics views |
| Academic KPIs | GPA/CGPA calculation + GPA simulator |
| UX | Responsive UI for desktop and mobile |

## Role Capabilities

### Admin

- Manage students, doctors, subjects, and exams
- View results and institutional statistics
- Monitor system activity

### Doctor

- Manage assigned subjects
- Create exams and questions
- Track student attempts
- Enter and maintain marks
- Analyze exam results

### Student

- View enrolled subjects
- Take online exams and submit answers
- Track results and GPA/CGPA
- Use GPA simulator for planning

## Technology Stack

| Technology | Purpose |
| --- | --- |
| Laravel 12 | Backend framework and MVC architecture |
| PHP 8.2+ | Server-side language |
| MySQL | Relational database |
| Blade | Server-rendered templating engine |
| Bootstrap 5 / Tailwind CSS 4 | UI styling and responsive components |
| JavaScript (ES Modules) | Frontend interactions |
| Vite | Asset bundling and dev server |
| Composer / NPM | Dependency management |
| XAMPP | Local development environment |

## Architecture

The project follows a role-aware MVC architecture:

- Models: academic entities, users, exams, attempts, and results
- Controllers: role-based workflows and business rules
- Views: Blade portals for Admin, Doctor, and Student
- Routes: organized in routes/web.php with prefixes and named routes
- Middleware: authentication and role authorization

## Database Model

| Table | Purpose | Relationships |
| --- | --- | --- |
| users | Authentication and role data | 1:1 with students or doctors |
| students | Student profile and KPI data | Belongs to users; many-to-many with subjects |
| doctors | Doctor profile and specialization | Belongs to users; many-to-many with subjects |
| subjects | Course metadata and credit hours | Many-to-many with students and doctors |
| student_subject | Student enrollment pivot | Connects students and subjects |
| doctor_subject | Doctor assignment pivot | Connects doctors and subjects |
| exams | Legacy/gradebook exam records | Belongs to doctors; used by results |
| exams_new | Online exams storage | Linked to online exam workflow |
| exam_questions | Question bank | Belongs to online exams |
| question_choices | MCQ options and correctness | Belongs to exam_questions |
| student_exam_attempts | Attempt session and score summary | Belongs to students and online exams |
| student_answers | Per-question answer data | Belongs to attempts and questions |
| results | Persisted marks and quality points | Belongs to students and exams |

## Quick Start

### 1) Clone Repository

```bash
git clone https://github.com/Nady-Emad/University-System.git
cd University-System
```

### 2) Install Dependencies

```bash
composer install
npm install
```

### 3) Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 4) Configure Database

Update .env:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=university_system
DB_USERNAME=root
DB_PASSWORD=
```

### 5) Migrate and Seed

```bash
php artisan migrate
php artisan db:seed
```

### 6) Run Frontend and Server

```bash
npm run dev
php artisan serve
```

Application URL: http://127.0.0.1:8000

## Default Credentials

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@university.com | password |
| Doctor | m.ali@university.com | password |
| Student | ahmed.hassan@student.com | password |

## Screenshots

| Module | Preview |
| --- | --- |
| Admin Dashboard | ![Admin Dashboard](docs/screenshots/admin-dashboard.png) |
| Doctor Dashboard | ![Doctor Dashboard](docs/screenshots/doctor-dashboard.png) |
| Student Portal | ![Student Portal](docs/screenshots/student-portal.png) |
| Exam Interface | ![Exam Interface](docs/screenshots/exam-interface.png) |

## Roadmap

- AI-assisted grading
- Online proctoring
- Question randomization strategies
- Mobile application
- Notification center
- Advanced analytics dashboards

## Author

University System Team

## License

Licensed under the MIT License.


