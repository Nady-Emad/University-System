@extends('layouts.app')

@section('title', 'Exams - University System')
@section('page-title', 'Exam Management')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Midterm Exams</div>
            <p class="stat-value">{{ $midtermCount }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Final Exams</div>
            <p class="stat-value">{{ $finalCount }}</p>
        </div>
    </div>
</div>

<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 justify-content-between">
        <form action="{{ route('exams.index') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by title, subject, type, year, or semester">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if ($search)
                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <a href="{{ route('exams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Exam
        </a>
    </div>
</div>

@php
    $groupedExams = $exams->getCollection()->groupBy('exam_type');
@endphp

@foreach (['midterm' => 'Midterm Exams', 'final' => 'Final Exams'] as $type => $title)
    <div class="panel-card table-card mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 mb-0">{{ $title }}</h2>
            <span class="exam-type-badge {{ $type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $type }}</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Exam</th>
                    <th>Date</th>
                    <th>Marks</th>
                    <th>CH</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th>Doctor</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($groupedExams->get($type, collect()) as $exam)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $exam->title }}</div>
                            <div class="small text-muted">{{ $exam->subject_name }}</div>
                        </td>
                        <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                        <td>{{ $exam->total_marks }}</td>
                        <td>{{ $exam->credit_hours }}</td>
                        <td>{{ $exam->semester }}</td>
                        <td>{{ $exam->academic_year }}</td>
                        <td>{{ $exam->doctor->full_name ?? '-' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-info">View</a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Delete this exam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"><div class="empty-state">No {{ $type }} exams found.</div></td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endforeach

@if ($exams->hasPages())
    <div class="panel-card p-3">{{ $exams->links() }}</div>
@endif
@endsection
