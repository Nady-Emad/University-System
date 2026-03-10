@extends('layouts.doctor')

@section('title', 'Edit Result - Doctor Portal')
@section('page-title', 'Update Student Marks')

@section('content')
<div class="panel-card p-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h2 class="h6 mb-1">{{ $result->student->full_name ?? '-' }}</h2>
            <div class="small text-muted">
                {{ $result->exam->title ?? '-' }} - {{ $result->exam->subject_name ?? '' }}
            </div>
        </div>
        @if ($result->exam)
            <span class="exam-type-badge {{ $result->exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $result->exam->exam_type }}</span>
        @endif
    </div>

    <form action="{{ route('doctor.results.update', $result->id) }}" method="POST" class="row g-3" novalidate>
        @csrf
        @method('PUT')

        <div class="col-md-4">
            <label class="form-label">Marks</label>
            <input type="number" step="0.01" min="0" max="{{ $result->exam->total_marks ?? 100 }}" name="marks" value="{{ old('marks', $result->marks) }}" class="form-control @error('marks') is-invalid @enderror" required>
            @error('marks')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Credit Hours</label>
            <input type="text" value="{{ $result->exam->credit_hours ?? $result->credit_hours }}" class="form-control" disabled>
        </div>

        <div class="col-md-4">
            <label class="form-label">Academic Term</label>
            <input type="text" value="{{ $result->semester }} / {{ $result->academic_year }}" class="form-control" disabled>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Update Result</button>
            <a href="{{ route('doctor.results') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
