<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\OnlineExam;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentExamAttempt;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class RealExamSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = $this->seedSubjects();
        $this->linkDoctors($subjects);
        $this->enrollStudents($subjects);
        $onlineExams = $this->seedOnlineExams($subjects);
        $this->seedQuestions($onlineExams);
        $this->seedSampleAttempt($onlineExams);
    }

    /** @return array<string, Subject> */
    private function seedSubjects(): array
    {
        $rows = [
            ['code' => 'CS101', 'name' => 'Programming 1', 'description' => 'Intro to programming fundamentals', 'credit_hours' => 3, 'semester' => 'Spring', 'academic_year' => '2025/2026'],
            ['code' => 'DB201', 'name' => 'Database Systems', 'description' => 'Relational databases and SQL', 'credit_hours' => 3, 'semester' => 'Spring', 'academic_year' => '2025/2026'],
            ['code' => 'NET301', 'name' => 'Computer Networks', 'description' => 'Network basics and protocols', 'credit_hours' => 3, 'semester' => 'Spring', 'academic_year' => '2025/2026'],
            ['code' => 'WEB205', 'name' => 'Web Development', 'description' => 'Frontend and backend basics', 'credit_hours' => 2, 'semester' => 'Spring', 'academic_year' => '2025/2026'],
            ['code' => 'SEC220', 'name' => 'Cyber Security Basics', 'description' => 'Security fundamentals', 'credit_hours' => 2, 'semester' => 'Spring', 'academic_year' => '2025/2026'],
        ];

        $subjects = [];

        foreach ($rows as $row) {
            $subject = Subject::updateOrCreate(['code' => $row['code']], $row);
            $subjects[$row['code']] = $subject;
        }

        return $subjects;
    }

    /** @param array<string, Subject> $subjects */
    private function linkDoctors(array $subjects): void
    {
        $map = [
            'm.ali@university.com' => ['CS101', 'WEB205'],
            'r.mostafa@university.com' => ['DB201'],
            'k.fathy@university.com' => ['NET301', 'SEC220'],
        ];

        foreach ($map as $email => $codes) {
            $doctor = Doctor::where('email', $email)->first();
            if (! $doctor) {
                continue;
            }

            $subjectIds = collect($codes)
                ->map(fn (string $code) => $subjects[$code]->id ?? null)
                ->filter()
                ->values()
                ->all();

            $doctor->subjects()->syncWithoutDetaching($subjectIds);
        }
    }

    /** @param array<string, Subject> $subjects */
    private function enrollStudents(array $subjects): void
    {
        $map = [
            'ahmed.hassan@student.com' => ['CS101', 'DB201', 'NET301', 'WEB205'],
            'mona.adel@student.com' => ['CS101', 'DB201', 'WEB205'],
            'youssef.samir@student.com' => ['DB201', 'NET301', 'SEC220'],
            'sara.nabil@student.com' => ['CS101', 'WEB205', 'SEC220'],
            'omar.khaled@student.com' => ['DB201', 'NET301', 'SEC220'],
            'nour.hany@student.com' => ['CS101', 'DB201', 'NET301'],
        ];

        foreach ($map as $email => $codes) {
            $student = Student::where('email', $email)->first();
            if (! $student) {
                continue;
            }

            $sync = [];
            foreach ($codes as $code) {
                if (! isset($subjects[$code])) {
                    continue;
                }
                $sync[$subjects[$code]->id] = ['enrollment_status' => 'enrolled'];
            }

            $student->subjects()->syncWithoutDetaching($sync);
        }
    }

    /** @param array<string, Subject> $subjects
     *  @return array<string, OnlineExam>
     */
    private function seedOnlineExams(array $subjects): array
    {
        $rows = [
            ['key' => 'cs_midterm', 'title' => 'Programming 1 Midterm Online', 'exam_type' => 'midterm', 'subject_code' => 'CS101', 'doctor_email' => 'm.ali@university.com', 'start_time' => '2026-03-20 10:00:00', 'end_time' => '2026-03-20 12:00:00', 'duration_minutes' => 60, 'total_marks' => 10, 'status' => 'published', 'allow_retake' => false],
            ['key' => 'db_final', 'title' => 'Database Systems Final Online', 'exam_type' => 'final', 'subject_code' => 'DB201', 'doctor_email' => 'r.mostafa@university.com', 'start_time' => '2026-05-24 09:00:00', 'end_time' => '2026-05-24 12:00:00', 'duration_minutes' => 90, 'total_marks' => 10, 'status' => 'published', 'allow_retake' => false],
            ['key' => 'net_midterm', 'title' => 'Computer Networks Midterm Online', 'exam_type' => 'midterm', 'subject_code' => 'NET301', 'doctor_email' => 'k.fathy@university.com', 'start_time' => '2026-03-25 11:00:00', 'end_time' => '2026-03-25 13:00:00', 'duration_minutes' => 60, 'total_marks' => 10, 'status' => 'published', 'allow_retake' => false],
            ['key' => 'sec_quiz', 'title' => 'Cyber Security Basics Quiz Online', 'exam_type' => 'quiz', 'subject_code' => 'SEC220', 'doctor_email' => 'k.fathy@university.com', 'start_time' => '2026-04-10 10:00:00', 'end_time' => '2026-04-10 11:00:00', 'duration_minutes' => 30, 'total_marks' => 5, 'status' => 'published', 'allow_retake' => true],
        ];

        $exams = [];

        foreach ($rows as $row) {
            $subject = $subjects[$row['subject_code']] ?? null;
            $doctor = Doctor::where('email', $row['doctor_email'])->first();

            if (! $subject || ! $doctor) {
                continue;
            }

            $exam = OnlineExam::updateOrCreate(
                ['title' => $row['title'], 'subject_id' => $subject->id, 'doctor_id' => $doctor->id],
                [
                    'exam_type' => $row['exam_type'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'duration_minutes' => $row['duration_minutes'],
                    'total_marks' => $row['total_marks'],
                    'status' => $row['status'],
                    'allow_retake' => $row['allow_retake'],
                ]
            );

            $exams[$row['key']] = $exam;
        }

        return $exams;
    }

    /** @param array<string, OnlineExam> $onlineExams */
    private function seedQuestions(array $onlineExams): void
    {
        $questions = [
            'cs_midterm' => [
                ['order' => 1, 'text' => 'Which is a valid C++ data type?', 'mark' => 2, 'choices' => [['integer', 0], ['int', 1], ['number', 0], ['real', 0]]],
                ['order' => 2, 'text' => 'Which loop executes at least once?', 'mark' => 2, 'choices' => [['for', 0], ['while', 0], ['do while', 1], ['foreach', 0]]],
                ['order' => 3, 'text' => 'Statement terminator in C++?', 'mark' => 2, 'choices' => [['.', 0], [':', 0], [';', 1], [',', 0]]],
                ['order' => 4, 'text' => 'Keyword for constant?', 'mark' => 2, 'choices' => [['const', 1], ['static', 0], ['fixed', 0], ['definevar', 0]]],
                ['order' => 5, 'text' => 'Equality operator?', 'mark' => 2, 'choices' => [['=', 0], ['==', 1], ['!=', 0], ['===', 0]]],
            ],
            'db_final' => [
                ['order' => 1, 'text' => 'SQL command to retrieve data?', 'mark' => 2, 'choices' => [['SELECT', 1], ['INSERT', 0], ['UPDATE', 0], ['DELETE', 0]]],
                ['order' => 2, 'text' => 'Normal form that removes partial dependency?', 'mark' => 2, 'choices' => [['1NF', 0], ['2NF', 1], ['3NF', 0], ['BCNF', 0]]],
                ['order' => 3, 'text' => 'Key that uniquely identifies a record?', 'mark' => 2, 'choices' => [['Foreign Key', 0], ['Candidate Key', 0], ['Primary Key', 1], ['Composite Key', 0]]],
                ['order' => 4, 'text' => 'JOIN for matching rows from both tables?', 'mark' => 2, 'choices' => [['LEFT JOIN', 0], ['RIGHT JOIN', 0], ['INNER JOIN', 1], ['CROSS JOIN', 0]]],
                ['order' => 5, 'text' => 'Statement to remove a table?', 'mark' => 2, 'choices' => [['DROP TABLE', 1], ['DELETE TABLE', 0], ['REMOVE TABLE', 0], ['TRUNCATE TABLE', 0]]],
            ],
        ];

        foreach ($questions as $examKey => $rows) {
            $exam = $onlineExams[$examKey] ?? null;
            if (! $exam) {
                continue;
            }

            foreach ($rows as $row) {
                $question = $exam->questions()->updateOrCreate(
                    ['order_no' => $row['order'], 'question_text' => $row['text']],
                    ['mark' => $row['mark']]
                );

                foreach ($row['choices'] as [$text, $isCorrect]) {
                    $question->choices()->updateOrCreate(
                        ['choice_text' => $text],
                        ['is_correct' => (bool) $isCorrect]
                    );
                }
            }

            $exam->update(['total_marks' => $exam->questions()->sum('mark')]);
        }
    }

    /** @param array<string, OnlineExam> $onlineExams */
    private function seedSampleAttempt(array $onlineExams): void
    {
        $student = Student::where('email', 'ahmed.hassan@student.com')->first();
        $exam = $onlineExams['cs_midterm'] ?? null;

        if (! $student || ! $exam) {
            return;
        }

        $attempt = StudentExamAttempt::updateOrCreate(
            ['student_id' => $student->id, 'exam_id' => $exam->id],
            [
                'started_at' => now()->subDays(2)->setTime(10, 5),
                'submitted_at' => now()->subDays(2)->setTime(10, 40),
                'status' => 'submitted',
                'total_marks' => $exam->total_marks,
            ]
        );

        $obtained = 0.0;
        $questions = $exam->questions()->with('choices')->orderBy('order_no')->get();

        foreach ($questions as $index => $question) {
            $choice = $index === 4
                ? $question->choices->firstWhere('is_correct', false)
                : $question->choices->firstWhere('is_correct', true);

            if (! $choice) {
                continue;
            }

            $isCorrect = (bool) $choice->is_correct;
            $mark = $isCorrect ? (float) $question->mark : 0.0;

            StudentAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                [
                    'selected_choice_id' => $choice->id,
                    'is_correct' => $isCorrect,
                    'obtained_mark' => $mark,
                ]
            );

            $obtained += $mark;
        }

        $attempt->recalculate($obtained);
    }
}
