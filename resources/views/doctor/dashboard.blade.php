@extends('layouts.doctor')

@section('title', 'Doctor Dashboard - University System')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Assigned Exams</div>
            <p class="stat-value">{{ $totalAssignedExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Students In My Exams</div>
            <p class="stat-value">{{ $totalStudentsInExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-title">Midterms</div>
            <p class="stat-value">{{ $totalMidtermExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-title">Finals</div>
            <p class="stat-value">{{ $totalFinalExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-title">Entered Results</div>
            <p class="stat-value">{{ $totalEnteredResults }}</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="stat-title">Online Exams</div>
            <p class="stat-value fs-3">{{ $totalOnlineExams }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="stat-title">Published Online Exams</div>
            <p class="stat-value fs-3">{{ $totalPublishedOnlineExams }}</p>
        </div>
    </div>
</div>

<div class="panel-card table-card mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h2 class="h6 mb-0">Recently Assigned Exams</h2>
        <a href="{{ route('doctor.exams') }}" class="btn btn-sm btn-outline-primary">Manage Exams</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Type</th>
                <th>Date</th>
                <th>Semester</th>
                <th>Results</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($assignedExams as $exam)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $exam->title }}</div>
                        <div class="small text-muted">{{ $exam->subject_name }}</div>
                    </td>
                    <td>
                        <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">
                            {{ $exam->exam_type }}
                        </span>
                    </td>
                    <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                    <td>{{ $exam->semester }} / {{ $exam->academic_year }}</td>
                    <td>{{ $exam->results_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('doctor.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"><div class="empty-state">No exams assigned yet.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel-card p-3 d-flex justify-content-between align-items-center">
    <div>
        <div class="fw-semibold">Real Exam-Taking Workspace</div>
        <div class="small text-muted">Create online exams, add questions, and track attempts.</div>
    </div>
    <a href="{{ route('doctor.online-exams.index') }}" class="btn btn-primary">Open Online Exams</a>
</div>
@endsection
