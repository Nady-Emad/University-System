<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $doctors = Doctor::query()
            ->with('user')
            ->withCount('exams')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('doctors.index', compact('doctors', 'search'));
    }

    public function create(): View
    {
        return view('doctors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:doctors,email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'specialization' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'doctor',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'specialization' => $validated['specialization'],
            ]);
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully with login account.');
    }

    public function show(Doctor $doctor): View
    {
        $doctor->load('exams');

        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor): View
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('doctors', 'email')->ignore($doctor->id),
                Rule::unique('users', 'email')->ignore($doctor->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'specialization' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated, $doctor): void {
            if ($doctor->user) {
                $userData = [
                    'name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'role' => 'doctor',
                ];

                if (! empty($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }

                $doctor->user->update($userData);
            }

            $doctor->update([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'specialization' => $validated['specialization'],
            ]);
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        DB::transaction(function () use ($doctor): void {
            if ($doctor->user) {
                $doctor->user->delete();
                return;
            }

            $doctor->delete();
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
