<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\OnlineExam;
use App\Models\Student;
use App\Models\StudentExamAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $student = $this->resolveStudent($request);

        $recentResults = $student->results()
            ->with('exam')
            ->latest()
            ->limit(5)
            ->get();

        $enrolledSubjects = $student->subjects()
            ->wherePivot('enrollment_status', 'enrolled')
            ->get(['subjects.id', 'subjects.name']);

        $enrolledSubjectIds = $enrolledSubjects->pluck('id');
        $enrolledSubjectNames = $enrolledSubjects->pluck('name');

        $upcomingExamsQuery = Exam::query()
            ->whereDate('exam_date', '>=', now()->toDateString());

        if ($enrolledSubjectNames->isNotEmpty()) {
            $upcomingExamsQuery->whereIn('subject_name', $enrolledSubjectNames);
        } else {
            $upcomingExamsQuery->whereRaw('1 = 0');
        }

        $upcomingExams = $upcomingExamsQuery
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        $onlineExamStats = [
            'available' => OnlineExam::query()
                ->whereIn('subject_id', $enrolledSubjectIds)
                ->where('status', 'published')
                ->where('end_time', '>=', now())
                ->count(),
            'in_progress' => StudentExamAttempt::query()
                ->where('student_id', $student->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => StudentExamAttempt::query()
                ->where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'auto_submitted'])
                ->count(),
        ];

        $nextOnlineExam = OnlineExam::query()
            ->with('subject')
            ->whereIn('subject_id', $enrolledSubjectIds)
            ->where('status', 'published')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->first();

        return view('student.dashboard', compact(
            'student',
            'recentResults',
            'upcomingExams',
            'onlineExamStats',
            'nextOnlineExam'
        ));
    }

    public function profile(Request $request): View
    {
        $student = $this->resolveStudent($request);

        return view('student.profile', compact('student'));
    }

    public function exams(Request $request): View
    {
        $student = $this->resolveStudent($request);

        $enrolledSubjects = $student->subjects()
            ->wherePivot('enrollment_status', 'enrolled')
            ->get(['subjects.id', 'subjects.name']);

        $enrolledSubjectIds = $enrolledSubjects->pluck('id');
        $enrolledSubjectNames = $enrolledSubjects->pluck('name');

        $examsQuery = Exam::query()
            ->with(['doctor', 'results' => fn ($query) => $query->where('student_id', $student->id)]);

        if ($enrolledSubjectNames->isNotEmpty()) {
            $examsQuery->whereIn('subject_name', $enrolledSubjectNames);
        } else {
            $examsQuery->whereRaw('1 = 0');
        }

        $exams = $examsQuery
            ->orderByDesc('exam_date')
            ->paginate(10);

        $onlineExams = OnlineExam::query()
            ->with(['subject', 'doctor'])
            ->whereIn('subject_id', $enrolledSubjectIds)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        $onlineAttempts = $student->onlineExamAttempts()
            ->whereIn('exam_id', $onlineExams->pluck('id'))
            ->get()
            ->groupBy('exam_id');

        return view('student.exams', compact('student', 'exams', 'onlineExams', 'onlineAttempts'));
    }

    public function results(Request $request): View
    {
        $student = $this->resolveStudent($request);

        $results = $student->results()
            ->with('exam')
            ->latest()
            ->paginate(10);

        $subjectSummaries = $student->subjectPerformanceSummaries()
            ->sort(function (array $a, array $b): int {
                if ($a['academic_year'] === $b['academic_year']) {
                    if ($a['semester'] === $b['semester']) {
                        return strcmp($a['subject_name'], $b['subject_name']);
                    }

                    $semesterOrder = ['Fall' => 1, 'Spring' => 2, 'Summer' => 3];
                    return ($semesterOrder[$a['semester']] ?? 99) <=> ($semesterOrder[$b['semester']] ?? 99);
                }

                return strcmp($b['academic_year'], $a['academic_year']);
            })
            ->values();

        $termSummaries = $student->termPerformanceSummaries();

        return view('student.results', compact('student', 'results', 'subjectSummaries', 'termSummaries'));
    }

    public function simulateGpa(Request $request): View
    {
        $student = $this->resolveStudent($request);

        return view('student.gpa-simulator', compact('student'));
    }

    private function resolveStudent(Request $request): Student
    {
        $student = $request->user()?->student;

        abort_if(! $student, 403, 'Student profile was not found for this account.');

        return $student;
    }
}
