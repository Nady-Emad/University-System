@extends('layouts.app')

@section('title', 'Result Details - University System')
@section('page-title', 'Result Details')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Result Information</h2>

    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Student</div>
            <div class="fw-semibold">{{ $result->student->full_name ?? '-' }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Student Code</div>
            <div class="fw-semibold">{{ $result->student->student_code ?? '-' }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Exam</div>
            <div class="fw-semibold">{{ $result->exam->title ?? '-' }}</div>
            <div class="small text-muted">{{ $result->exam->subject_name ?? '' }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Exam Type</div>
            @if ($result->exam)
                <span class="exam-type-badge {{ $result->exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $result->exam->exam_type }}</span>
            @endif
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Assigned Doctor</div>
            <div class="fw-semibold">{{ $result->exam->doctor->full_name ?? '-' }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Marks</div>
            <div class="fw-semibold">{{ number_format((float) $result->marks, 2) }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Grade Point</div>
            <div class="fw-semibold">{{ number_format((float) $result->grade_point, 2) }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Credit Hours</div>
            <div class="fw-semibold">{{ $result->credit_hours }}</div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="text-muted small">Quality Points</div>
            <div class="fw-semibold">{{ number_format((float) $result->quality_points, 2) }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Semester</div>
            <div class="fw-semibold">{{ $result->semester }}</div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="text-muted small">Academic Year</div>
            <div class="fw-semibold">{{ $result->academic_year }}</div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('results.edit', $result) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        <a href="{{ route('results.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>
@endsection
