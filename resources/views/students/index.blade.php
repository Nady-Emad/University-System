@extends('layouts.app')

@section('title', 'Students - University System')
@section('page-title', 'Student Management')

@section('content')
<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 justify-content-between">
        <form action="{{ route('students.index') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by name, email, code, or phone">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if ($search)
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <a href="{{ route('students.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Student
        </a>
    </div>
</div>

<div class="panel-card table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Student</th>
                <th>Code</th>
                <th>Entry Year</th>
                <th>Status</th>
                <th class="text-center">GPA</th>
                <th class="text-center">CGPA</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($students as $student)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $student->full_name }}</div>
                        <div class="small text-muted">{{ $student->email }}</div>
                    </td>
                    <td>{{ $student->student_code }}</td>
                    <td>{{ $student->entry_year }}</td>
                    <td>
                        <span class="badge text-bg-{{ $student->status === 'active' ? 'success' : 'secondary' }} text-capitalize">{{ $student->status }}</span>
                    </td>
                    <td class="text-center">{{ number_format((float) $student->current_gpa, 2) }}</td>
                    <td class="text-center">{{ number_format((float) $student->current_cgpa, 2) }}</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Delete this student?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">No students found. Start by creating a new student record.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="p-3 border-top">{{ $students->links() }}</div>
    @endif
</div>
@endsection
