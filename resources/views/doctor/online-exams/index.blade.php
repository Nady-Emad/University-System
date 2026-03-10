@extends('layouts.doctor')

@section('title', 'Online Exams - Doctor Portal')
@section('page-title', 'Online Exam Management')

@section('content')
<div class="panel-card p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <form action="{{ route('doctor.online-exams.index') }}" method="GET" class="d-flex gap-2 w-100 w-lg-50">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by title, type, status">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if ($search)
                <a href="{{ route('doctor.online-exams.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createOnlineExamForm" aria-expanded="false" aria-controls="createOnlineExamForm">
            <i class="bi bi-plus-circle me-1"></i> Create Online Exam
        </button>
    </div>

    <div class="collapse mt-3" id="createOnlineExamForm">
        <div class="border rounded-3 p-3 bg-light-subtle">
            <h2 class="h6 mb-3">New Online Exam</h2>

            @if ($subjects->isEmpty())
                <div class="alert alert-warning mb-0">No subject is assigned to your profile. Ask admin to assign subject(s) first.</div>
            @else
                <form action="{{ route('doctor.online-exams.store') }}" method="POST" class="row g-3" novalidate>
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Exam Type</label>
                        <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                            @foreach (['midterm', 'final', 'quiz', 'practical'] as $type)
                                <option value="{{ $type }}" @selected(old('exam_type', 'midterm') === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>
                                    {{ $subject->code }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Start Time</label>
                        <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">End Time</label>
                        <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Duration (min)</label>
                        <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', 60) }}" min="5" max="300" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Total Marks</label>
                        <input type="number" step="0.01" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', 10) }}" min="1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (['draft', 'published', 'closed'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="allow_retake" name="allow_retake" @checked(old('allow_retake'))>
                            <label class="form-check-label" for="allow_retake">Allow retake</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Save Online Exam</button>
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#createOnlineExamForm">Close</button>
                    </div>
                </form>
            @endif
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
                <th>Subject</th>
                <th>Status</th>
                <th>Window</th>
                <th class="text-center">Questions</th>
                <th class="text-center">Attempts</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($onlineExams as $exam)
                <tr>
                    <td class="fw-semibold">{{ $exam->title }}</td>
                    <td>
                        <span class="exam-type-badge exam-type-{{ $exam->exam_type }}">{{ $exam->exam_type }}</span>
                    </td>
                    <td>{{ $exam->subject->code ?? '-' }} - {{ $exam->subject->name ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $exam->status === 'published' ? 'success' : ($exam->status === 'draft' ? 'warning' : 'secondary') }} text-capitalize">{{ $exam->status }}</span></td>
                    <td>
                        <div>{{ $exam->start_time?->format('Y-m-d H:i') }}</div>
                        <div class="small text-muted">to {{ $exam->end_time?->format('Y-m-d H:i') }}</div>
                    </td>
                    <td class="text-center">{{ $exam->questions_count }}</td>
                    <td class="text-center">{{ $exam->attempts_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('doctor.online-exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"><div class="empty-state">No online exams found.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($onlineExams->hasPages())
        <div class="p-3 border-top">{{ $onlineExams->links() }}</div>
    @endif
</div>
@endsection

