@extends('layouts.app')

@section('title', 'Subjects - University System')
@section('page-title', 'Subject Management')

@section('content')
<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <form action="{{ route('subjects.index') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by code, name, semester, or year">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if ($search)
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <a href="{{ route('subjects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Subject
        </a>
    </div>
</div>

<div class="panel-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Code</th>
                <th>Subject</th>
                <th class="text-center">Credit Hours</th>
                <th>Semester / Year</th>
                <th class="text-center">Doctors</th>
                <th class="text-center">Students</th>
                <th class="text-center">Online Exams</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($subjects as $subject)
                <tr>
                    <td class="fw-semibold">{{ $subject->code }}</td>
                    <td>
                        <div class="fw-semibold">{{ $subject->name }}</div>
                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($subject->description, 70) ?: 'No description' }}</div>
                    </td>
                    <td class="text-center">{{ $subject->credit_hours }}</td>
                    <td>{{ $subject->semester }} / {{ $subject->academic_year }}</td>
                    <td class="text-center">{{ $subject->doctors_count }}</td>
                    <td class="text-center">{{ $subject->students_count }}</td>
                    <td class="text-center">{{ $subject->online_exams_count }}</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Delete this subject and related links?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"><div class="empty-state">No subjects found yet.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($subjects->hasPages())
        <div class="p-3 border-top">{{ $subjects->links() }}</div>
    @endif
</div>
@endsection
