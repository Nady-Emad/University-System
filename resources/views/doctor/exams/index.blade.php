@extends('layouts.doctor')

@section('title', 'My Exams - Doctor Portal')
@section('page-title', 'My Midterm and Final Exams')

@section('content')
<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <form action="{{ route('doctor.exams') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by title, subject, type, semester, year">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if ($search)
                <a href="{{ route('doctor.exams') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createExamForm" aria-expanded="false" aria-controls="createExamForm">
            <i class="bi bi-plus-circle me-1"></i> Create Exam
        </button>
    </div>

    <div class="collapse mt-3" id="createExamForm">
        <div class="border rounded-3 p-3 bg-light-subtle">
            <h2 class="h6 mb-3">Create New Exam</h2>
            <form action="{{ route('doctor.exams.store') }}" method="POST" class="row g-3" novalidate>
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="subject_name" class="form-control @error('subject_name') is-invalid @enderror" value="{{ old('subject_name') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Exam Type</label>
                    <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                        <option value="midterm" @selected(old('exam_type') === 'midterm')>Midterm</option>
                        <option value="final" @selected(old('exam_type') === 'final')>Final</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Exam Date</label>
                    <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror" value="{{ old('exam_date') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', 100) }}" min="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Credit Hours</label>
                    <input type="number" name="credit_hours" class="form-control @error('credit_hours') is-invalid @enderror" value="{{ old('credit_hours', 3) }}" min="1" max="12" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                        @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                            <option value="{{ $semester }}" @selected(old('semester', 'Spring') === $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" value="{{ old('academic_year', now()->year . '/' . (now()->year + 1)) }}" required>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Exam</button>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#createExamForm">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Type</th>
                <th>Date</th>
                <th>Marks</th>
                <th>CH</th>
                <th>Semester</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($exams as $exam)
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
                    <td>{{ $exam->total_marks }}</td>
                    <td>{{ $exam->credit_hours }}</td>
                    <td>{{ $exam->semester }} / {{ $exam->academic_year }}</td>
                    <td class="text-end">
                        <a href="{{ route('doctor.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7"><div class="empty-state">No exams found for your account.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($exams->hasPages())
        <div class="p-3 border-top">{{ $exams->links() }}</div>
    @endif
</div>
@endsection
