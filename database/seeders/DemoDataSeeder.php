<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use App\Support\AcademicCalculator;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = $this->seedDoctors();
        $students = $this->seedStudents();
        $exams = $this->seedExams($doctors);

        $this->seedResults($students, $exams);

        foreach ($students as $student) {
            $student->refreshPerformance();
        }
    }

    /**
     * @return array<string, Doctor>
     */
    private function seedDoctors(): array
    {
        $definitions = [
            [
                'full_name' => 'Dr. Mohamed Ali',
                'email' => 'm.ali@university.com',
                'phone' => '01211111111',
                'specialization' => 'Computer Science',
            ],
            [
                'full_name' => 'Dr. Reem Mostafa',
                'email' => 'r.mostafa@university.com',
                'phone' => '01222222222',
                'specialization' => 'Information Systems',
            ],
            [
                'full_name' => 'Dr. Karim Fathy',
                'email' => 'k.fathy@university.com',
                'phone' => '01233333333',
                'specialization' => 'Networks and Cyber Security',
            ],
        ];

        $doctors = [];

        foreach ($definitions as $definition) {
            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['full_name'],
                    'password' => 'password',
                    'role' => 'doctor',
                ]
            );

            $doctor = Doctor::firstOrNew(['user_id' => $user->id]);
            $doctor->fill([
                'full_name' => $definition['full_name'],
                'email' => $definition['email'],
                'phone' => $definition['phone'],
                'specialization' => $definition['specialization'],
            ]);
            $doctor->save();

            $doctors[$definition['email']] = $doctor;
        }

        return $doctors;
    }

    /**
     * @return array<string, Student>
     */
    private function seedStudents(): array
    {
        $definitions = [
            [
                'full_name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@student.com',
                'phone' => '01012345678',
                'entry_year' => 2024,
                'student_code' => '2024-0001',
                'status' => 'active',
            ],
            [
                'full_name' => 'Mona Adel',
                'email' => 'mona.adel@student.com',
                'phone' => '01022345678',
                'entry_year' => 2024,
                'student_code' => '2024-0002',
                'status' => 'active',
            ],
            [
                'full_name' => 'Youssef Samir',
                'email' => 'youssef.samir@student.com',
                'phone' => '01032345678',
                'entry_year' => 2025,
                'student_code' => '2025-0001',
                'status' => 'active',
            ],
            [
                'full_name' => 'Sara Nabil',
                'email' => 'sara.nabil@student.com',
                'phone' => '01042345678',
                'entry_year' => 2025,
                'student_code' => '2025-0002',
                'status' => 'active',
            ],
            [
                'full_name' => 'Omar Khaled',
                'email' => 'omar.khaled@student.com',
                'phone' => '01052345678',
                'entry_year' => 2023,
                'student_code' => '2023-0007',
                'status' => 'active',
            ],
            [
                'full_name' => 'Nour Hany',
                'email' => 'nour.hany@student.com',
                'phone' => '01062345678',
                'entry_year' => 2023,
                'student_code' => '2023-0008',
                'status' => 'active',
            ],
        ];

        $students = [];

        foreach ($definitions as $definition) {
            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['full_name'],
                    'password' => 'password',
                    'role' => 'student',
                ]
            );

            $student = Student::firstOrNew(['user_id' => $user->id]);
            $student->fill([
                'full_name' => $definition['full_name'],
                'email' => $definition['email'],
                'phone' => $definition['phone'],
                'entry_year' => $definition['entry_year'],
                'student_code' => $definition['student_code'],
                'status' => $definition['status'],
            ]);
            $student->save();

            $students[$definition['email']] = $student;
        }

        return $students;
    }

    /**
     * @param array<string, Doctor> $doctors
     * @return array<string, Exam>
     */
    private function seedExams(array $doctors): array
    {
        $definitions = [
            [
                'key' => 'programming_midterm',
                'title' => 'Programming 1 Midterm',
                'subject_name' => 'Programming 1',
                'exam_type' => 'midterm',
                'exam_date' => '2026-03-15',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'm.ali@university.com',
            ],
            [
                'key' => 'programming_final',
                'title' => 'Programming 1 Final',
                'subject_name' => 'Programming 1',
                'exam_type' => 'final',
                'exam_date' => '2026-05-20',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'm.ali@university.com',
            ],
            [
                'key' => 'database_midterm',
                'title' => 'Database Systems Midterm',
                'subject_name' => 'Database Systems',
                'exam_type' => 'midterm',
                'exam_date' => '2026-03-18',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'r.mostafa@university.com',
            ],
            [
                'key' => 'database_final',
                'title' => 'Database Systems Final',
                'subject_name' => 'Database Systems',
                'exam_type' => 'final',
                'exam_date' => '2026-05-24',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'r.mostafa@university.com',
            ],
            [
                'key' => 'networks_midterm',
                'title' => 'Computer Networks Midterm',
                'subject_name' => 'Computer Networks',
                'exam_type' => 'midterm',
                'exam_date' => '2026-03-22',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'k.fathy@university.com',
            ],
            [
                'key' => 'networks_final',
                'title' => 'Computer Networks Final',
                'subject_name' => 'Computer Networks',
                'exam_type' => 'final',
                'exam_date' => '2026-05-28',
                'total_marks' => 100,
                'credit_hours' => 3,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'k.fathy@university.com',
            ],
            [
                'key' => 'web_midterm',
                'title' => 'Web Development Midterm',
                'subject_name' => 'Web Development',
                'exam_type' => 'midterm',
                'exam_date' => '2026-03-25',
                'total_marks' => 100,
                'credit_hours' => 2,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'm.ali@university.com',
            ],
            [
                'key' => 'web_final',
                'title' => 'Web Development Final',
                'subject_name' => 'Web Development',
                'exam_type' => 'final',
                'exam_date' => '2026-05-30',
                'total_marks' => 100,
                'credit_hours' => 2,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'm.ali@university.com',
            ],
            [
                'key' => 'cyber_midterm',
                'title' => 'Cyber Security Basics Midterm',
                'subject_name' => 'Cyber Security Basics',
                'exam_type' => 'midterm',
                'exam_date' => '2026-04-02',
                'total_marks' => 100,
                'credit_hours' => 2,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'k.fathy@university.com',
            ],
            [
                'key' => 'cyber_final',
                'title' => 'Cyber Security Basics Final',
                'subject_name' => 'Cyber Security Basics',
                'exam_type' => 'final',
                'exam_date' => '2026-06-05',
                'total_marks' => 100,
                'credit_hours' => 2,
                'semester' => 'Spring',
                'academic_year' => '2025/2026',
                'doctor_email' => 'k.fathy@university.com',
            ],
        ];

        $exams = [];

        foreach ($definitions as $definition) {
            $doctor = $doctors[$definition['doctor_email']] ?? null;
            if (! $doctor) {
                continue;
            }

            $exam = Exam::updateOrCreate(
                [
                    'title' => $definition['title'],
                    'subject_name' => $definition['subject_name'],
                    'exam_type' => $definition['exam_type'],
                    'academic_year' => $definition['academic_year'],
                ],
                [
                    'exam_date' => $definition['exam_date'],
                    'total_marks' => $definition['total_marks'],
                    'credit_hours' => $definition['credit_hours'],
                    'semester' => $definition['semester'],
                    'doctor_id' => $doctor->id,
                ]
            );

            $exams[$definition['key']] = $exam;
        }

        return $exams;
    }

    /**
     * @param array<string, Student> $students
     * @param array<string, Exam> $exams
     */
    private function seedResults(array $students, array $exams): void
    {
        $definitions = [
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'programming_midterm', 'marks' => 88.00],
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'programming_final', 'marks' => 91.00],
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'database_midterm', 'marks' => 84.00],
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'database_final', 'marks' => 87.00],
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'networks_midterm', 'marks' => 79.00],
            ['student_email' => 'ahmed.hassan@student.com', 'exam_key' => 'networks_final', 'marks' => 82.00],

            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'programming_midterm', 'marks' => 71.00],
            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'programming_final', 'marks' => 76.00],
            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'database_midterm', 'marks' => 68.00],
            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'database_final', 'marks' => 74.00],
            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'web_midterm', 'marks' => 81.00],
            ['student_email' => 'mona.adel@student.com', 'exam_key' => 'web_final', 'marks' => 86.00],

            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'networks_midterm', 'marks' => 65.00],
            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'networks_final', 'marks' => 73.00],
            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'cyber_midterm', 'marks' => 90.00],
            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'cyber_final', 'marks' => 94.00],
            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'database_midterm', 'marks' => 58.00],
            ['student_email' => 'youssef.samir@student.com', 'exam_key' => 'database_final', 'marks' => 63.00],

            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'programming_midterm', 'marks' => 92.00],
            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'programming_final', 'marks' => 95.00],
            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'web_midterm', 'marks' => 88.00],
            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'web_final', 'marks' => 91.00],
            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'cyber_midterm', 'marks' => 85.00],
            ['student_email' => 'sara.nabil@student.com', 'exam_key' => 'cyber_final', 'marks' => 89.00],

            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'database_midterm', 'marks' => 77.00],
            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'database_final', 'marks' => 83.00],
            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'networks_midterm', 'marks' => 69.00],
            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'networks_final', 'marks' => 72.00],
            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'cyber_midterm', 'marks' => 80.00],
            ['student_email' => 'omar.khaled@student.com', 'exam_key' => 'cyber_final', 'marks' => 84.00],

            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'programming_midterm', 'marks' => 60.00],
            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'programming_final', 'marks' => 67.00],
            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'database_midterm', 'marks' => 74.00],
            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'database_final', 'marks' => 79.00],
            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'networks_midterm', 'marks' => 82.00],
            ['student_email' => 'nour.hany@student.com', 'exam_key' => 'networks_final', 'marks' => 86.00],
        ];

        foreach ($definitions as $definition) {
            $student = $students[$definition['student_email']] ?? null;
            $exam = $exams[$definition['exam_key']] ?? null;

            if (! $student || ! $exam) {
                continue;
            }

            $gradePoint = AcademicCalculator::gradePointFromMarks((float) $definition['marks']);
            $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, (int) $exam->credit_hours);

            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'exam_id' => $exam->id,
                ],
                [
                    'marks' => $definition['marks'],
                    'grade_point' => $gradePoint,
                    'credit_hours' => $exam->credit_hours,
                    'quality_points' => $qualityPoints,
                    'semester' => $exam->semester,
                    'academic_year' => $exam->academic_year,
                ]
            );
        }
    }
}
