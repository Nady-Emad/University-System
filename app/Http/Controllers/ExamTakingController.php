<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\OnlineExam;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentExamAttempt;
use App\Support\AcademicCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamTakingController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->resolveStudent($request);

        $subjectIds = $student->subjects()
            ->wherePivot('enrollment_status', 'enrolled')
            ->pluck('subjects.id');

        $exams = OnlineExam::query()
            ->with(['subject', 'doctor'])
            ->whereIn('subject_id', $subjectIds)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('start_time')
            ->get();

        $attempts = $student->onlineExamAttempts()
            ->whereIn('exam_id', $exams->pluck('id'))
            ->get()
            ->groupBy('exam_id');

        return view('student.online-exams.index', compact('student', 'exams', 'attempts'));
    }

    public function show(int $examId, Request $request): View|RedirectResponse
    {
        $student = $this->resolveStudent($request);
        $exam = $this->resolveAccessibleExam($student, $examId);

        $submittedAttempt = $student->onlineExamAttempts()
            ->with('answers')
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest('submitted_at')
            ->first();

        if ($submittedAttempt && ! $exam->allow_retake) {
            return view('student.online-exams.show', [
                'student' => $student,
                'exam' => $exam,
                'attempt' => $submittedAttempt,
                'answersByQuestion' => $submittedAttempt->answers->keyBy('question_id'),
                'isReadOnly' => true,
                'remainingSeconds' => 0,
            ]);
        }

        if ($exam->status !== 'published') {
            return redirect()->route('student.online-exams.index')
                ->with('error', 'This exam is not published.');
        }

        if (now()->lessThan($exam->start_time) || now()->greaterThan($exam->end_time)) {
            return redirect()->route('student.online-exams.index')
                ->with('error', 'This exam is not currently available.');
        }

        $attempt = $student->onlineExamAttempts()
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->first();

        if (! $attempt) {
            $attempt = StudentExamAttempt::create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'started_at' => now(),
                'total_marks' => $exam->total_marks,
                'status' => 'in_progress',
            ]);
        }

        $deadline = $attempt->deadline();

        if (now()->greaterThan($deadline)) {
            $this->finalizeAttempt($attempt, [], true);

            return redirect()->route('student.online-exams.show', $exam->id)
                ->with('error', 'Time is over. Your attempt was auto-submitted.');
        }

        $answersByQuestion = $attempt->answers()->get()->keyBy('question_id');

        return view('student.online-exams.show', [
            'student' => $student,
            'exam' => $exam,
            'attempt' => $attempt,
            'answersByQuestion' => $answersByQuestion,
            'isReadOnly' => false,
            'remainingSeconds' => max(0, now()->diffInSeconds($deadline, false)),
        ]);
    }

    public function submit(int $examId, Request $request): RedirectResponse
    {
        $student = $this->resolveStudent($request);
        $exam = $this->resolveAccessibleExam($student, $examId);

        $submittedAttempt = $student->onlineExamAttempts()
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->exists();

        if ($submittedAttempt && ! $exam->allow_retake) {
            return redirect()->route('student.online-exams.show', $exam->id)
                ->with('error', 'You have already submitted this exam.');
        }

        $attempt = $student->onlineExamAttempts()
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->first();

        if (! $attempt) {
            return redirect()->route('student.online-exams.show', $exam->id)
                ->with('error', 'No active attempt was found. Start the exam first.');
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer', 'exists:question_choices,id'],
        ]);

        $answers = $validated['answers'] ?? [];
        $isAutoSubmitted = now()->greaterThan($attempt->deadline());

        $this->finalizeAttempt($attempt, $answers, $isAutoSubmitted);

        return redirect()->route('student.online-exams.show', $exam->id)
            ->with('success', 'Exam submitted successfully.');
    }

    /**
     * @param array<int|string, int|string|null> $answers
     */
    private function finalizeAttempt(StudentExamAttempt $attempt, array $answers, bool $isAutoSubmitted): void
    {
        $attempt->loadMissing(['exam.subject', 'exam.questions.choices', 'student']);

        DB::transaction(function () use ($attempt, $answers, $isAutoSubmitted): void {
            $obtainedMarks = 0.0;

            foreach ($attempt->exam->questions as $question) {
                $questionId = (int) $question->id;
                $selectedChoiceId = isset($answers[$questionId]) ? (int) $answers[$questionId] : null;

                $choice = $selectedChoiceId
                    ? $question->choices->firstWhere('id', $selectedChoiceId)
                    : null;

                $isCorrect = (bool) ($choice?->is_correct ?? false);
                $obtainedMark = $isCorrect ? (float) $question->mark : 0.0;

                StudentAnswer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'selected_choice_id' => $choice?->id,
                        'is_correct' => $isCorrect,
                        'obtained_mark' => $obtainedMark,
                    ]
                );

                $obtainedMarks += $obtainedMark;
            }

            $attempt->update([
                'submitted_at' => now(),
                'status' => $isAutoSubmitted ? 'auto_submitted' : 'submitted',
            ]);

            $attempt->recalculate($obtainedMarks);

            $this->syncLegacyResult($attempt);
        });
    }

    private function syncLegacyResult(StudentExamAttempt $attempt): void
    {
        $exam = $attempt->exam;

        if (! in_array($exam->exam_type, ['midterm', 'final'], true)) {
            return;
        }

        $legacyExam = Exam::query()->where('source_online_exam_id', $exam->id)->first();

        if (! $legacyExam) {
            $legacyExam = Exam::query()
                ->whereNull('source_online_exam_id')
                ->where('subject_name', $exam->subject->name)
                ->where('exam_type', $exam->exam_type)
                ->where('doctor_id', $exam->doctor_id)
                ->where('semester', $exam->subject->semester)
                ->where('academic_year', $exam->subject->academic_year)
                ->first();
        }

        if ($legacyExam) {
            $legacyExam->update([
                'title' => $exam->title,
                'subject_name' => $exam->subject->name,
                'exam_type' => $exam->exam_type,
                'exam_date' => $exam->end_time->toDateString(),
                'total_marks' => 100,
                'credit_hours' => $exam->subject->credit_hours,
                'semester' => $exam->subject->semester,
                'academic_year' => $exam->subject->academic_year,
                'doctor_id' => $exam->doctor_id,
                'source_online_exam_id' => $exam->id,
            ]);
        } else {
            $legacyExam = Exam::create([
                'title' => $exam->title,
                'subject_name' => $exam->subject->name,
                'exam_type' => $exam->exam_type,
                'exam_date' => $exam->end_time->toDateString(),
                'total_marks' => 100,
                'credit_hours' => $exam->subject->credit_hours,
                'semester' => $exam->subject->semester,
                'academic_year' => $exam->subject->academic_year,
                'doctor_id' => $exam->doctor_id,
                'source_online_exam_id' => $exam->id,
            ]);
        }

        $gradePoint = AcademicCalculator::gradePointFromMarks((float) $attempt->percentage);
        $creditHours = (int) $exam->subject->credit_hours;
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, $creditHours);

        Result::updateOrCreate(
            [
                'student_id' => $attempt->student_id,
                'exam_id' => $legacyExam->id,
            ],
            [
                'marks' => $attempt->percentage,
                'grade_point' => $gradePoint,
                'credit_hours' => $creditHours,
                'quality_points' => $qualityPoints,
                'semester' => $exam->subject->semester,
                'academic_year' => $exam->subject->academic_year,
            ]
        );

        $attempt->student->refreshPerformance();
    }

    private function resolveAccessibleExam(Student $student, int $examId): OnlineExam
    {
        $exam = OnlineExam::query()
            ->with(['subject', 'doctor', 'questions.choices'])
            ->findOrFail($examId);

        $isEnrolled = $student->subjects()
            ->where('subjects.id', $exam->subject_id)
            ->wherePivot('enrollment_status', 'enrolled')
            ->exists();

        abort_if(! $isEnrolled, 403, 'You are not enrolled in this subject.');

        return $exam;
    }

    private function resolveStudent(Request $request): Student
    {
        $student = $request->user()?->student;

        abort_if(! $student, 403, 'Student profile was not found for this account.');

        return $student;
    }
}

