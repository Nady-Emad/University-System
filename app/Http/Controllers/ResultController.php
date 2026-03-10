<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Support\AcademicCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $selectedStudentId = $request->integer('student_id');
        $selectedSemester = (string) $request->input('semester', '');
        $selectedAcademicYear = trim((string) $request->input('academic_year', ''));
        $selectedExamType = (string) $request->input('exam_type', '');

        $results = Result::query()
            ->with(['student', 'exam.doctor'])
            ->when($selectedStudentId > 0, fn ($query) => $query->where('student_id', $selectedStudentId))
            ->when($selectedSemester !== '', fn ($query) => $query->where('semester', $selectedSemester))
            ->when($selectedAcademicYear !== '', fn ($query) => $query->where('academic_year', $selectedAcademicYear))
            ->when($selectedExamType !== '', fn ($query) => $query->whereHas('exam', fn ($q) => $q->where('exam_type', $selectedExamType)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $students = Student::orderBy('full_name')->get();

        $gpaSummaries = Student::query()
            ->select('id', 'full_name', 'student_code', 'current_gpa', 'current_cgpa', 'total_completed_credit_hours')
            ->orderByDesc('current_cgpa')
            ->get();

        return view('results.index', compact('results', 'students', 'gpaSummaries'));
    }

    public function create(): View
    {
        $students = Student::orderBy('full_name')->get();
        $exams = Exam::with('doctor')->orderByDesc('exam_date')->get();

        return view('results.create', compact('students', 'exams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'exam_id' => [
                'required',
                'exists:exams,id',
                Rule::unique('results', 'exam_id')->where(fn ($query) => $query->where('student_id', $request->input('student_id'))),
            ],
            'marks' => ['required', 'numeric', 'min:0'],
            'credit_hours' => ['nullable', 'integer', 'min:1', 'max:12'],
            'semester' => ['nullable', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        if ((float) $validated['marks'] > (float) $exam->total_marks) {
            return back()
                ->withErrors(['marks' => "Marks cannot exceed the exam total ({$exam->total_marks})."])
                ->withInput();
        }

        $creditHours = (int) ($validated['credit_hours'] ?? $exam->credit_hours);
        $gradePoint = AcademicCalculator::gradePointFromMarks((float) $validated['marks']);
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, $creditHours);
        $semester = $validated['semester'] ?? $exam->semester;
        $academicYear = $validated['academic_year'] ?? $exam->academic_year;

        DB::transaction(function () use ($validated, $creditHours, $gradePoint, $qualityPoints, $semester, $academicYear): void {
            Result::create([
                'student_id' => $validated['student_id'],
                'exam_id' => $validated['exam_id'],
                'marks' => $validated['marks'],
                'grade_point' => $gradePoint,
                'credit_hours' => $creditHours,
                'quality_points' => $qualityPoints,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ]);

            Student::findOrFail($validated['student_id'])->refreshPerformance();
        });

        return redirect()->route('results.index')->with('success', 'Result created and GPA/CGPA updated successfully.');
    }

    public function show(Result $result): View
    {
        $result->load(['student', 'exam.doctor']);

        return view('results.show', compact('result'));
    }

    public function edit(Result $result): View
    {
        $students = Student::orderBy('full_name')->get();
        $exams = Exam::with('doctor')->orderByDesc('exam_date')->get();

        return view('results.edit', compact('result', 'students', 'exams'));
    }

    public function update(Request $request, Result $result): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'exam_id' => [
                'required',
                'exists:exams,id',
                Rule::unique('results', 'exam_id')
                    ->ignore($result->id)
                    ->where(fn ($query) => $query->where('student_id', $request->input('student_id'))),
            ],
            'marks' => ['required', 'numeric', 'min:0'],
            'credit_hours' => ['nullable', 'integer', 'min:1', 'max:12'],
            'semester' => ['nullable', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        if ((float) $validated['marks'] > (float) $exam->total_marks) {
            return back()
                ->withErrors(['marks' => "Marks cannot exceed the exam total ({$exam->total_marks})."])
                ->withInput();
        }

        $oldStudentId = $result->student_id;
        $newStudentId = (int) $validated['student_id'];

        $creditHours = (int) ($validated['credit_hours'] ?? $exam->credit_hours);
        $gradePoint = AcademicCalculator::gradePointFromMarks((float) $validated['marks']);
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, $creditHours);
        $semester = $validated['semester'] ?? $exam->semester;
        $academicYear = $validated['academic_year'] ?? $exam->academic_year;

        DB::transaction(function () use (
            $result,
            $validated,
            $creditHours,
            $gradePoint,
            $qualityPoints,
            $semester,
            $academicYear,
            $oldStudentId,
            $newStudentId
        ): void {
            $result->update([
                'student_id' => $validated['student_id'],
                'exam_id' => $validated['exam_id'],
                'marks' => $validated['marks'],
                'grade_point' => $gradePoint,
                'credit_hours' => $creditHours,
                'quality_points' => $qualityPoints,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ]);

            Student::findOrFail($newStudentId)->refreshPerformance();

            if ($oldStudentId !== $newStudentId) {
                Student::findOrFail($oldStudentId)->refreshPerformance();
            }
        });

        return redirect()->route('results.index')->with('success', 'Result updated and GPA/CGPA refreshed successfully.');
    }

    public function destroy(Result $result): RedirectResponse
    {
        $studentId = $result->student_id;

        DB::transaction(function () use ($result, $studentId): void {
            $result->delete();
            Student::findOrFail($studentId)->refreshPerformance();
        });

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
