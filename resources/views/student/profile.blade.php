@extends('layouts.student')

@section('title', 'My Profile - University System')

@section('content')
<div class="panel-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">My Profile</h1>
        <span class="badge text-bg-primary">Student</span>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Full Name</div>
            <div class="fw-semibold">{{ $student->full_name }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Email</div>
            <div class="fw-semibold">{{ $student->email }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Phone</div>
            <div class="fw-semibold">{{ $student->phone ?: '-' }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Student Code</div>
            <div class="fw-semibold">{{ $student->student_code }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Entry Year</div>
            <div class="fw-semibold">{{ $student->entry_year }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Status</div>
            <div class="fw-semibold text-capitalize">{{ $student->status }}</div>
        </div>
    </div>

    <hr>

    <div class="row g-3">
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Current GPA</div>
            <div class="fw-semibold">{{ number_format((float) $student->current_gpa, 2) }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Current CGPA</div>
            <div class="fw-semibold">{{ number_format((float) $student->current_cgpa, 2) }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Completed Credit Hours</div>
            <div class="fw-semibold">{{ $student->total_completed_credit_hours }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Total Quality Points</div>
            <div class="fw-semibold">{{ number_format((float) $student->total_quality_points, 2) }}</div>
        </div>
    </div>
</div>
@endsection
