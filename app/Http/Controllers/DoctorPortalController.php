<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Exam;
use App\Models\OnlineExam;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentExamAttempt;
use App\Models\Subject;
use App\Support\AcademicCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $totalAssignedExams = $doctor->exams()->count();
        $totalMidtermExams = $doctor->exams()->where('exam_type', 'midterm')->count();
        $totalFinalExams = $doctor->exams()->where('exam_type', 'final')->count();

        $totalLegacyResults = Result::query()
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->count();

        $totalSubmittedOnlineAttempts = StudentExamAttempt::query()
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->count();

        $totalEnteredResults = $totalLegacyResults + $totalSubmittedOnlineAttempts;

        $totalStudentsInExams = Student::query()
            ->whereHas('subjects.onlineExams', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->distinct('students.id')
            ->count('students.id');

        $assignedExams = $doctor->exams()
            ->withCount('results')
            ->orderByDesc('exam_date')
            ->limit(8)
            ->get();

        $totalOnlineExams = $doctor->onlineExams()->count();
        $totalPublishedOnlineExams = $doctor->onlineExams()->where('status', 'published')->count();

        return view('doctor.dashboard', compact(
            'doctor',
            'totalAssignedExams',
            'totalStudentsInExams',
            'totalMidtermExams',
            'totalFinalExams',
            'totalEnteredResults',
            'assignedExams',
            'totalOnlineExams',
            'totalPublishedOnlineExams'
        ));
    }

    public function exams(Request $request): View
    {
        $doctor = $this->resolveDoctor($request);
        $search = trim((string) $request->query('q'));

        $exams = $doctor->exams()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('subject_name', 'like', "%{$search}%")
                        ->orWhere('exam_type', 'like', "%{$search}%")
                        ->orWhere('semester', 'like', "%{$search}%")
                        ->orWhere('academic_year', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("FIELD(exam_type, 'midterm', 'final')")
            ->orderByDesc('exam_date')
            ->paginate(10)
            ->withQueryString();

        return view('doctor.exams.index', compact('doctor', 'exams', 'search'));
    }

    public function showExam(int $id, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $exam = $doctor->exams()
            ->with(['results.student'])
            ->findOrFail($id);

        $subject = $this->resolveSubjectForLegacyExam($exam);

        $students = $subject
            ? $subject->students()
                ->wherePivot('enrollment_status', 'enrolled')
                ->orderBy('full_name')
                ->get(['students.*'])
            : Student::query()->orderBy('full_name')->get();

        $resultsByStudent = $exam->results->keyBy('student_id');

        $studentRows = $students->map(function (Student $student) use ($resultsByStudent): array {
            /** @var Result|null $result */
            $result = $resultsByStudent->get($student->id);

            return [
                'student' => $student,
                'result' => $result,
            ];
        });

        $isSubjectLinked = $subject !== null;

        return view('doctor.exams.show', compact('doctor', 'exam', 'studentRows', 'isSubjectLinked'));
    }

    public function results(Request $request): View
    {
        $doctor = $this->resolveDoctor($request);
        $examType = (string) $request->input('exam_type', '');

        $results = Result::query()
            ->with(['student', 'exam'])
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->when($examType !== '', fn ($query) => $query->whereHas('exam', fn ($sub) => $sub->where('exam_type', $examType)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $exams = $doctor->exams()
            ->orderByDesc('exam_date')
            ->get();

        return view('doctor.results.index', compact('doctor', 'results', 'exams'));
    }

    public function createResult(int $examId, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $exam = $doctor->exams()->with('results.student')->findOrFail($examId);

        $subject = $this->resolveSubjectForLegacyExam($exam);

        $students = $subject
            ? $subject->students()->wherePivot('enrollment_status', 'enrolled')->orderBy('full_name')->get(['students.*'])
            : Student::query()->orderBy('full_name')->get();

        $existingResults = $exam->results->keyBy('student_id');
        $isSubjectLinked = $subject !== null;
        $preselectedStudentId = $request->integer('student_id') > 0 ? $request->integer('student_id') : null;

        return view('doctor.results.create', compact('doctor', 'exam', 'students', 'existingResults', 'isSubjectLinked', 'preselectedStudentId'));
    }

    public function storeResult(Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $validated = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'student_id' => ['required', 'exists:students,id'],
            'marks' => ['required', 'numeric', 'min:0'],
        ]);

        $exam = $doctor->exams()->findOrFail((int) $validated['exam_id']);

        if (! $this->isStudentEligibleForLegacyExam($exam, (int) $validated['student_id'])) {
            return back()
                ->withErrors(['student_id' => 'This student is not enrolled in the exam subject.'])
                ->withInput();
        }

        if ((float) $validated['marks'] > (float) $exam->total_marks) {
            return back()
                ->withErrors(['marks' => "Marks cannot exceed the exam total ({$exam->total_marks})."])
                ->withInput();
        }

        $gradePoint = AcademicCalculator::gradePointFromMarks((float) $validated['marks']);
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, (int) $exam->credit_hours);

        Result::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'exam_id' => $exam->id,
            ],
            [
                'marks' => $validated['marks'],
                'grade_point' => $gradePoint,
                'credit_hours' => $exam->credit_hours,
                'quality_points' => $qualityPoints,
                'semester' => $exam->semester,
                'academic_year' => $exam->academic_year,
            ]
        );

        Student::findOrFail((int) $validated['student_id'])->refreshPerformance();

        return redirect()->route('doctor.results')->with('success', 'Result saved successfully.');
    }

    public function editResult(int $id, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $result = Result::query()
            ->with(['student', 'exam'])
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->findOrFail($id);

        return view('doctor.results.edit', compact('doctor', 'result'));
    }

    public function updateResult(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $validated = $request->validate([
            'marks' => ['required', 'numeric', 'min:0'],
        ]);

        $result = Result::query()
            ->with('exam')
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->findOrFail($id);

        if ((float) $validated['marks'] > (float) $result->exam->total_marks) {
            return back()
                ->withErrors(['marks' => "Marks cannot exceed the exam total ({$result->exam->total_marks})."])
                ->withInput();
        }

        $gradePoint = AcademicCalculator::gradePointFromMarks((float) $validated['marks']);
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, (int) $result->exam->credit_hours);

        $result->update([
            'marks' => $validated['marks'],
            'grade_point' => $gradePoint,
            'credit_hours' => $result->exam->credit_hours,
            'quality_points' => $qualityPoints,
            'semester' => $result->exam->semester,
            'academic_year' => $result->exam->academic_year,
        ]);

        $result->student->refreshPerformance();

        return redirect()->route('doctor.results')->with('success', 'Result updated successfully.');
    }

    public function storeExam(Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $validated = $request->validate($this->examValidationRules());

        $doctor->exams()->create($validated);

        return redirect()->route('doctor.exams')->with('success', 'Exam created successfully.');
    }

    public function updateExam(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);
        $exam = $doctor->exams()->findOrFail($id);

        $validated = $request->validate($this->examValidationRules());

        $exam->update($validated);

        return redirect()->route('doctor.exams.show', $exam->id)->with('success', 'Exam updated successfully.');
    }

    public function destroyExam(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);
        $exam = $doctor->exams()->findOrFail($id);

        $exam->delete();

        return redirect()->route('doctor.exams')->with('success', 'Exam deleted successfully.');
    }

    public function onlineExams(Request $request): View
    {
        $doctor = $this->resolveDoctor($request);
        $search = trim((string) $request->query('q'));

        $onlineExams = $doctor->onlineExams()
            ->with(['subject'])
            ->withCount(['questions', 'attempts'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('exam_type', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        $subjects = $doctor->subjects()
            ->orderBy('name')
            ->get(['subjects.id', 'subjects.code', 'subjects.name', 'subjects.semester', 'subjects.academic_year']);

        return view('doctor.online-exams.index', compact('doctor', 'onlineExams', 'subjects', 'search'));
    }

    public function storeOnlineExam(Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);
        $allowedSubjectIds = $doctor->subjects()->pluck('subjects.id')->all();

        if ($allowedSubjectIds === []) {
            return redirect()->route('doctor.online-exams.index')
                ->with('error', 'No subject is assigned to your profile. Ask admin to assign a subject first.');
        }

        $validated = $request->validate($this->onlineExamValidationRules($allowedSubjectIds));

        $doctor->onlineExams()->create($validated + [
            'allow_retake' => $request->boolean('allow_retake'),
        ]);

        return redirect()->route('doctor.online-exams.index')->with('success', 'Online exam created successfully.');
    }

    public function showOnlineExam(int $id, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $exam = $doctor->onlineExams()
            ->with(['subject', 'questions.choices'])
            ->findOrFail($id);

        $studentAttemptRows = $this->buildEnrolledStudentAttemptRows($exam);

        $attemptSummary = [
            'submitted' => $studentAttemptRows->where('status_key', 'submitted')->count(),
            'in_progress' => $studentAttemptRows->where('status_key', 'in_progress')->count(),
            'not_started' => $studentAttemptRows->where('status_key', 'not_started')->count(),
        ];

        return view('doctor.online-exams.show', compact('doctor', 'exam', 'attemptSummary', 'studentAttemptRows'));
    }

    public function updateOnlineExam(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $exam = $doctor->onlineExams()->findOrFail($id);
        $allowedSubjectIds = $doctor->subjects()->pluck('subjects.id')->all();

        $validated = $request->validate($this->onlineExamValidationRules($allowedSubjectIds));

        $exam->update($validated + [
            'allow_retake' => $request->boolean('allow_retake'),
        ]);

        return redirect()->route('doctor.online-exams.show', $exam->id)->with('success', 'Online exam updated successfully.');
    }

    public function destroyOnlineExam(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $exam = $doctor->onlineExams()->findOrFail($id);
        $exam->delete();

        return redirect()->route('doctor.online-exams.index')->with('success', 'Online exam deleted successfully.');
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildEnrolledStudentAttemptRows(OnlineExam $exam): Collection
    {
        $latestAttemptIds = StudentExamAttempt::query()
            ->selectRaw('MAX(id) as latest_id, student_id')
            ->where('exam_id', $exam->id)
            ->groupBy('student_id');

        $latestAttemptsForExam = StudentExamAttempt::query()
            ->from('student_exam_attempts as sea')
            ->select([
                'sea.id',
                'sea.student_id',
                'sea.status',
                'sea.obtained_marks',
                'sea.total_marks',
                'sea.percentage',
                'sea.submitted_at',
            ])
            ->joinSub($latestAttemptIds, 'latest', function ($join): void {
                $join->on('latest.latest_id', '=', 'sea.id');
            });

        $rows = Student::query()
            ->select([
                'students.id as student_id',
                'students.full_name',
                'students.student_code',
                'sea.id as attempt_id',
                'sea.status as attempt_status',
                'sea.obtained_marks',
                'sea.total_marks',
                'sea.percentage',
                'sea.submitted_at',
            ])
            ->join('student_subject', function ($join) use ($exam): void {
                $join->on('student_subject.student_id', '=', 'students.id')
                    ->where('student_subject.subject_id', '=', $exam->subject_id)
                    ->where('student_subject.enrollment_status', '=', 'enrolled');
            })
            ->leftJoinSub($latestAttemptsForExam, 'sea', function ($join): void {
                $join->on('sea.student_id', '=', 'students.id');
            })
            ->orderBy('students.full_name')
            ->get();

        return $rows->map(function ($row): array {
            $statusKey = 'not_started';
            $statusLabel = 'Not Started';

            if ($row->attempt_id) {
                if ($row->submitted_at !== null || in_array($row->attempt_status, ['submitted', 'auto_submitted'], true)) {
                    $statusKey = 'submitted';
                    $statusLabel = 'Submitted';
                } elseif ($row->attempt_status === 'in_progress') {
                    $statusKey = 'in_progress';
                    $statusLabel = 'In Progress';
                }
            }

            return [
                'student_id' => (int) $row->student_id,
                'full_name' => (string) $row->full_name,
                'student_code' => (string) $row->student_code,
                'attempt_id' => $row->attempt_id ? (int) $row->attempt_id : null,
                'status_key' => $statusKey,
                'status_label' => $statusLabel,
                'obtained_marks' => $row->obtained_marks !== null ? (float) $row->obtained_marks : null,
                'total_marks' => $row->total_marks !== null ? (float) $row->total_marks : null,
                'percentage' => $row->percentage !== null ? (float) $row->percentage : null,
                'submitted_at' => $row->submitted_at ? Carbon::parse($row->submitted_at) : null,
            ];
        });
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    private function examValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject_name' => ['required', 'string', 'max:255'],
            'exam_type' => ['required', Rule::in(['midterm', 'final'])],
            'exam_date' => ['required', 'date'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @param array<int, int> $allowedSubjectIds
     * @return array<string, array<int, string|Rule>>
     */
    private function onlineExamValidationRules(array $allowedSubjectIds): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'exam_type' => ['required', Rule::in(['midterm', 'final', 'quiz', 'practical'])],
            'subject_id' => ['required', 'integer', Rule::in($allowedSubjectIds)],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:300'],
            'total_marks' => ['required', 'numeric', 'min:1', 'max:1000'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
            'allow_retake' => ['nullable', 'boolean'],
        ];
    }

    private function resolveSubjectForLegacyExam(Exam $exam): ?Subject
    {
        $normalizedSubjectName = strtolower(trim((string) $exam->subject_name));

        if ($normalizedSubjectName === '') {
            return null;
        }

        $subject = Subject::query()
            ->whereRaw('LOWER(name) = ?', [$normalizedSubjectName])
            ->where('semester', $exam->semester)
            ->where('academic_year', $exam->academic_year)
            ->first();

        if ($subject) {
            return $subject;
        }

        return Subject::query()
            ->whereRaw('LOWER(name) = ?', [$normalizedSubjectName])
            ->first();
    }

    private function isStudentEligibleForLegacyExam(Exam $exam, int $studentId): bool
    {
        $subject = $this->resolveSubjectForLegacyExam($exam);

        if (! $subject) {
            return true;
        }

        return $subject->students()
            ->wherePivot('enrollment_status', 'enrolled')
            ->where('students.id', $studentId)
            ->exists();
    }

    private function resolveDoctor(Request $request): Doctor
    {
        $doctor = $request->user()?->doctor;

        abort_if(! $doctor, 403, 'Doctor profile was not found for this account.');

        return $doctor;
    }
}


