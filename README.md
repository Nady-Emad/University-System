# University Management System

> A Laravel-based platform for managing university operations, online exams, and student academic performance from one unified system.

## Project Overview

The **University Management System** is designed to help universities digitize core academic workflows, including user management, subject enrollment, online exam delivery, grading, and performance analytics.

Built with **Laravel**, this system supports three main user roles: **Admin**, **Doctor**, and **Student**. It enables institutions to manage students and doctors efficiently, run real online exams, and track GPA/CGPA in a structured, scalable way.

## Features
- Role-based authentication (Admin / Doctor / Student)
- Student management
- Doctor management
- Subject management
- Subject enrollment
- Real exam system
- Midterm and Final exams
- Question bank system
- Multiple-choice questions
- Online exam attempts
- Automatic grading
- GPA and CGPA calculation
- Doctor dashboard
- Student dashboard
- Admin dashboard
- Exam statistics
- Attempt tracking
- Responsive UI

## System Roles

### Admin

- Manage students
- Manage doctors
- Manage subjects
- Manage exams
- View results
- Monitor statistics

### Doctor

- Manage assigned subjects
- Create exams
- Add questions
- Monitor student attempts
- Enter and manage marks
- View exam statistics

### Student

- View enrolled subjects
- Take online exams
- Submit answers
- View results
- View GPA and CGPA
- Use GPA simulator

## Technology Stack

| Technology | Purpose |
| --- | --- |
| Laravel 12 | Backend framework and MVC architecture |
| PHP 8.2+ | Core server-side programming language |
| MySQL | Relational database |
| Blade | Server-rendered templating engine |
| Bootstrap 5 / Tailwind CSS 4 | UI styling and responsive components |
| JavaScript (ES Modules) | Frontend interactions |
| Vite | Asset bundling and development server |
| Git | Version control |
| Composer | PHP dependency management |
| XAMPP | Local development environment |

## Database Structure

The system uses relational tables to support authentication, academic data, exams, and grading workflows.

| Table | Purpose | Main Relationships |
| --- | --- | --- |
| `users` | Authentication and role storage | 1:1 with `students` or `doctors` |
| `students` | Student profile and academic KPIs | Belongs to `users`; many-to-many with `subjects`; has many `results` and `student_exam_attempts` |
| `doctors` | Doctor profile and specialization | Belongs to `users`; many-to-many with `subjects`; has many `exams` |
| `subjects` | Course metadata (code, credit hours, semester) | Many-to-many with `students` and `doctors`; has many online exams |
| `student_subject` | Student enrollment pivot table | Connects `students` and `subjects` |
| `doctor_subject` | Doctor assignment pivot table | Connects `doctors` and `subjects` |
| `exams` | Legacy/gradebook exam records (midterm/final) | Belongs to `doctors`; referenced by `results`; may link to online exam source |
| `exam_questions` | Question bank per online exam | Belongs to online exam (`exam_id`) |
| `question_choices` | MCQ options with correctness flag | Belongs to `exam_questions` |
| `student_exam_attempts` | Student exam session and score summary | Belongs to `students` and online exams |
| `student_answers` | Answer-level data for each attempt | Belongs to `student_exam_attempts` and `exam_questions` |
| `results` | Persisted marks and GPA quality points | Belongs to `students` and `exams` |

> Note: In this project, online exams are stored in a dedicated table (`exams_new`) and connected to `exams` for result synchronization.

## System Architecture

This project follows Laravel's **MVC architecture**:

- **Models**: Represent business entities such as users, students, subjects, exams, attempts, and results.
- **Controllers**: Handle role-based business logic, exam workflows, and dashboard data.
- **Views**: Blade templates provide separate interfaces for Admin, Doctor, and Student portals.
- **Routes**: Organized in `routes/web.php` with role prefixes and named routes.
- **Middleware**: Authentication and role-based access control secure each portal.

## Installation Guide
### 1. Clone the Repository

```bash
git clone <repository-url>
cd university-system
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Create Environment File

```bash
cp .env.example .env
```

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Configure Database

Update `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=university_system
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Run Seeders (if available)

```bash
php artisan db:seed
```

### 8. Build/Run Frontend Assets

```bash
npm run dev
```

### 9. Start the Server

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Default Login Credentials

> These accounts are available after running seeders.

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@university.com` | `password` |
| Doctor | `m.ali@university.com` | `password` |
| Student | `ahmed.hassan@student.com` | `password` |

## Screenshots
### Admin Dashboard
![Admin Dashboard](docs/screenshots/admin-dashboard.png)

### Doctor Dashboard
![Doctor Dashboard](docs/screenshots/doctor-dashboard.png)

### Student Portal
![Student Portal](docs/screenshots/student-portal.png)

### Exam Interface
![Exam Interface](docs/screenshots/exam-interface.png)

## Future Improvements
- AI-based grading
- Exam proctoring
- Question randomization
- Mobile app
- Notifications
- Analytics dashboard

## Author

**University System Team**

## License

This project is licensed under the **MIT License**. See the `LICENSE` file for details.


