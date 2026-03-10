<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $students = Student::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('students.index', compact('students', 'search'));
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'entry_year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:' . (now()->year + 1)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive', 'graduated', 'suspended'])],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'entry_year' => $validated['entry_year'],
                'student_code' => $this->generateStudentCode((int) $validated['entry_year']),
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student): View
    {
        $student->load(['user', 'results.exam']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'entry_year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:' . (now()->year + 1)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive', 'graduated', 'suspended'])],
        ]);

        DB::transaction(function () use ($validated, $student): void {
            if ($student->user) {
                $userData = [
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                ];

                if (! empty($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }

                $student->user->update($userData);
            }

            $studentCode = (int) $validated['entry_year'] !== (int) $student->entry_year
                ? $this->generateStudentCode((int) $validated['entry_year'], $student->id)
                : $student->student_code;

            $student->update([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'entry_year' => $validated['entry_year'],
                'student_code' => $studentCode,
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student): void {
            if ($student->user) {
                $student->user->delete();
                return;
            }

            $student->delete();
        });

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    private function generateStudentCode(int $entryYear, ?int $ignoreStudentId = null): string
    {
        $prefix = substr((string) $entryYear, -2);

        $query = Student::query()
            ->where('entry_year', $entryYear)
            ->where('student_code', 'like', $prefix . '%');

        if ($ignoreStudentId !== null) {
            $query->where('id', '!=', $ignoreStudentId);
        }

        $lastCode = $query->orderByDesc('student_code')->value('student_code');

        $sequence = 1;

        if ($lastCode) {
            $digits = preg_replace('/\D/', '', $lastCode);
            $sequence = (int) substr($digits, -4) + 1;
        }

        do {
            $generatedCode = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $exists = Student::query()
                ->where('student_code', $generatedCode)
                ->when($ignoreStudentId !== null, fn ($q) => $q->where('id', '!=', $ignoreStudentId))
                ->exists();
            $sequence++;
        } while ($exists);

        return $generatedCode;
    }
}
