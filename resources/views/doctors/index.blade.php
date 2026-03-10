@extends('layouts.app')

@section('title', 'Doctors - University System')
@section('page-title', 'Doctor / Lecturer Management')

@section('content')
<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 justify-content-between">
        <form action="{{ route('doctors.index') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by name, email, phone, specialization">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if ($search)
                <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <a href="{{ route('doctors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Doctor
        </a>
    </div>
</div>

<div class="panel-card table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Name</th>
                <th>Login Email</th>
                <th>Phone</th>
                <th>Specialization</th>
                <th class="text-center">Assigned Exams</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($doctors as $doctor)
                <tr>
                    <td class="fw-semibold">{{ $doctor->full_name }}</td>
                    <td>{{ $doctor->email }}</td>
                    <td>{{ $doctor->phone ?: '-' }}</td>
                    <td>{{ $doctor->specialization }}</td>
                    <td class="text-center">{{ $doctor->exams_count }}</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Delete this doctor account?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">No doctors found. Add your first doctor profile.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($doctors->hasPages())
        <div class="p-3 border-top">{{ $doctors->links() }}</div>
    @endif
</div>
@endsection
