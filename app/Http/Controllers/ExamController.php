<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $query = Exam::query()
            ->with('doctor')
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('subject_name', 'like', "%{$search}%")
                        ->orWhere('exam_type', 'like', "%{$search}%")
                        ->orWhere('academic_year', 'like', "%{$search}%")
                        ->orWhere('semester', 'like', "%{$search}%");
                });
            });

        $midtermCount = (clone $query)->where('exam_type', 'midterm')->count();
        $finalCount = (clone $query)->where('exam_type', 'final')->count();

        $exams = $query
            ->orderByRaw("FIELD(exam_type, 'midterm', 'final')")
            ->orderByDesc('exam_date')
            ->paginate(12)
            ->withQueryString();

        return view('exams.index', compact('exams', 'search', 'midtermCount', 'finalCount'));
    }

    public function create(): View
    {
        $doctors = Doctor::orderBy('full_name')->get();

        return view('exams.create', compact('doctors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_name' => ['required', 'string', 'max:255'],
            'exam_type' => ['required', Rule::in(['midterm', 'final'])],
            'exam_date' => ['required', 'date'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['required', 'string', 'max:20'],
            'doctor_id' => ['required', 'exists:doctors,id'],
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam): View
    {
        $exam->load(['doctor', 'results.student']);

        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam): View
    {
        $doctors = Doctor::orderBy('full_name')->get();

        return view('exams.edit', compact('exam', 'doctors'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_name' => ['required', 'string', 'max:255'],
            'exam_type' => ['required', Rule::in(['midterm', 'final'])],
            'exam_date' => ['required', 'date'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['required', 'string', 'max:20'],
            'doctor_id' => ['required', 'exists:doctors,id'],
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }
}
