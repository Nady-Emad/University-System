@extends('layouts.app')

@section('title', 'Doctor Details - University System')
@section('page-title', 'Doctor / Lecturer Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card p-4 h-100">
            <h2 class="h6 mb-3">Profile</h2>
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Full Name</dt>
                <dd class="col-7">{{ $doctor->full_name }}</dd>

                <dt class="col-5 text-muted">Email</dt>
                <dd class="col-7">{{ $doctor->email }}</dd>

                <dt class="col-5 text-muted">Phone</dt>
                <dd class="col-7">{{ $doctor->phone ?: '-' }}</dd>

                <dt class="col-5 text-muted">Specialization</dt>
                <dd class="col-7">{{ $doctor->specialization }}</dd>

                <dt class="col-5 text-muted">Role</dt>
                <dd class="col-7 text-capitalize">{{ $doctor->user->role ?? 'doctor' }}</dd>
            </dl>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Assigned Exams</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Exam Title</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Academic Year</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($doctor->exams as $exam)
                        <tr>
                            <td>{{ $exam->title }}</td>
                            <td>
                                <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $exam->exam_type }}</span>
                            </td>
                            <td>{{ $exam->subject_name }}</td>
                            <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                            <td>{{ $exam->academic_year }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No exams assigned to this doctor yet.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
