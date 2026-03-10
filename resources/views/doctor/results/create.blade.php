@extends('layouts.doctor')

@section('title', 'Enter Result - Doctor Portal')
@section('page-title', 'Enter Student Marks')

@section('content')
<div class="panel-card p-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h2 class="h6 mb-1">{{ $exam->title }} - {{ $exam->subject_name }}</h2>
            <div class="small text-muted">{{ $exam->semester }} / {{ $exam->academic_year }} | Total: {{ $exam->total_marks }} | CH: {{ $exam->credit_hours }}</div>
            <div class="small text-muted">All linked students are listed. Existing marks will be updated if already entered.</div>
        </div>
        <span class="exam-type-badge exam-type-{{ $exam->exam_type }}">{{ $exam->exam_type }}</span>
    </div>

    @if (! $isSubjectLinked)
        <div class="alert alert-warning">
            This exam subject is not linked to the Subjects module yet. All students are listed until the subject mapping is fixed.
        </div>
    @endif

    <form action="{{ route('doctor.results.store') }}" method="POST" class="row g-3" novalidate>
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        <div class="col-md-8">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                <option value="">Select student</option>
                @foreach ($students as $student)
                    @php($existing = $existingResults->get($student->id))
                    <option value="{{ $student->id }}" @selected((string) old('student_id', $preselectedStudentId ?? '') === (string) $student->id)>
                        {{ $student->full_name }} ({{ $student->student_code }}){{ $existing ? ' - existing: ' . number_format((float) $existing->marks, 2) : '' }}
                    </option>
                @endforeach
            </select>
            @error('student_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Marks</label>
            <input type="number" step="0.01" min="0" max="{{ $exam->total_marks }}" name="marks" value="{{ old('marks') }}" class="form-control @error('marks') is-invalid @enderror" required>
            @error('marks')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit" @disabled($students->isEmpty())>Save Result</button>
            <a href="{{ route('doctor.results') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if ($students->isEmpty())
            <div class="col-12">
                <div class="empty-state">No students are available for this exam yet.</div>
            </div>
        @endif
    </form>
</div>
@endsection
