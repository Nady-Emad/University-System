@extends('layouts.app')

@section('title', 'Dashboard - University System')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Students</div>
            <p class="stat-value">{{ $totalStudents }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Doctors</div>
            <p class="stat-value">{{ $totalDoctors }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Exams</div>
            <p class="stat-value">{{ $totalExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Results</div>
            <p class="stat-value">{{ $totalResults }}</p>
        </div>
    </div>
</div>

<div class="panel-card table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h2 class="h6 mb-0">Top Students by CGPA</h2>
        <a href="{{ route('results.index') }}" class="btn btn-sm btn-outline-primary">View All Results</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Student</th>
                <th>Code</th>
                <th class="text-center">Current GPA</th>
                <th class="text-center">Current CGPA</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($topStudents as $student)
                <tr>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td class="text-center">{{ number_format((float) $student->current_gpa, 2) }}</td>
                    <td class="text-center fw-semibold">{{ number_format((float) $student->current_cgpa, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">No student GPA records found yet.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
