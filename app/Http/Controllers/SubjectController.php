<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $subjects = Subject::query()
            ->withCount(['doctors', 'students', 'onlineExams'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('semester', 'like', "%{$search}%")
                        ->orWhere('academic_year', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate(12)
            ->withQueryString();

        return view('subjects.index', compact('subjects', 'search'));
    }

    public function create(): View
    {
        $doctors = Doctor::orderBy('full_name')->get(['id', 'full_name', 'specialization']);
        $students = Student::orderBy('full_name')->get(['id', 'full_name', 'student_code']);

        return view('subjects.create', compact('doctors', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $subject = Subject::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'credit_hours' => $validated['credit_hours'],
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
        ]);

        $subject->doctors()->sync($validated['doctor_ids'] ?? []);
        $this->syncStudents($subject, $validated['student_ids'] ?? []);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject): View
    {
        $subject->load([
            'doctors:id,full_name,specialization',
            'students:id,full_name,student_code',
            'onlineExams' => fn ($query) => $query->with('doctor:id,full_name')->orderByDesc('start_time'),
        ]);

        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject): View
    {
        $subject->load(['doctors:id', 'students:id']);

        $doctors = Doctor::orderBy('full_name')->get(['id', 'full_name', 'specialization']);
        $students = Student::orderBy('full_name')->get(['id', 'full_name', 'student_code']);

        return view('subjects.edit', compact('subject', 'doctors', 'students'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $this->validateRequest($request, $subject->id);

        $subject->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'credit_hours' => $validated['credit_hours'],
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
        ]);

        $subject->doctors()->sync($validated['doctor_ids'] ?? []);
        $this->syncStudents($subject, $validated['student_ids'] ?? []);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateRequest(Request $request, ?int $subjectId = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($subjectId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'credit_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', Rule::in(['Fall', 'Spring', 'Summer'])],
            'academic_year' => ['required', 'string', 'max:20'],
            'doctor_ids' => ['nullable', 'array'],
            'doctor_ids.*' => ['integer', 'exists:doctors,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);
    }

    /**
     * @param array<int, int|string> $studentIds
     */
    private function syncStudents(Subject $subject, array $studentIds): void
    {
        $syncData = [];

        foreach ($studentIds as $studentId) {
            $syncData[(int) $studentId] = ['enrollment_status' => 'enrolled'];
        }

        $subject->students()->sync($syncData);
    }
}
