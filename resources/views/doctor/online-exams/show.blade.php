@extends('layouts.doctor')

@section('title', 'Online Exam Details - Doctor Portal')
@section('page-title', 'Online Exam Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="h6 mb-0">Exam Information</h2>
                <span class="exam-type-badge exam-type-{{ $exam->exam_type }}">{{ $exam->exam_type }}</span>
            </div>

            <dl class="row mb-0">
                <dt class="col-5 text-muted">Title</dt>
                <dd class="col-7">{{ $exam->title }}</dd>

                <dt class="col-5 text-muted">Subject</dt>
                <dd class="col-7">{{ $exam->subject->code }} - {{ $exam->subject->name }}</dd>

                <dt class="col-5 text-muted">Status</dt>
                <dd class="col-7"><span class="badge text-bg-{{ $exam->status === 'published' ? 'success' : ($exam->status === 'draft' ? 'warning' : 'secondary') }} text-capitalize">{{ $exam->status }}</span></dd>

                <dt class="col-5 text-muted">Start</dt>
                <dd class="col-7">{{ $exam->start_time?->format('Y-m-d H:i') }}</dd>

                <dt class="col-5 text-muted">End</dt>
                <dd class="col-7">{{ $exam->end_time?->format('Y-m-d H:i') }}</dd>

                <dt class="col-5 text-muted">Duration</dt>
                <dd class="col-7">{{ $exam->duration_minutes }} min</dd>

                <dt class="col-5 text-muted">Total Marks</dt>
                <dd class="col-7">{{ number_format((float) $exam->total_marks, 2) }}</dd>
            </dl>
        </div>

        <div class="panel-card p-4 mb-4">
            <h2 class="h6 mb-3">Edit Online Exam</h2>
            <form action="{{ route('doctor.online-exams.update', $exam->id) }}" method="POST" class="row g-3" novalidate>
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $exam->title) }}" required>
                </div>

                <div class="col-6">
                    <label class="form-label">Exam Type</label>
                    <select name="exam_type" class="form-select" required>
                        @foreach (['midterm', 'final', 'quiz', 'practical'] as $type)
                            <option value="{{ $type }}" @selected(old('exam_type', $exam->exam_type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" required>
                        @foreach ($doctor->subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) old('subject_id', $exam->subject_id) === (string) $subject->id)>{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="col-6">
                    <label class="form-label">End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="col-4">
                    <label class="form-label">Duration (min)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="5" max="300" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required>
                </div>

                <div class="col-4">
                    <label class="form-label">Total Marks</label>
                    <input type="number" step="0.01" name="total_marks" class="form-control" min="1" value="{{ old('total_marks', $exam->total_marks) }}" required>
                </div>

                <div class="col-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach (['draft', 'published', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $exam->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" value="1" id="allow_retake" name="allow_retake" @checked(old('allow_retake', $exam->allow_retake))>
                        <label class="form-check-label" for="allow_retake">Allow retake</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>

            <form action="{{ route('doctor.online-exams.destroy', $exam->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this online exam with all questions and attempts?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete Online Exam</button>
            </form>
        </div>

        <div class="panel-card p-4">
            <h2 class="h6 mb-3">Attempt Summary</h2>
            <div class="small text-muted mb-2">Based on enrolled students in this subject</div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-success">Submitted: {{ (int) ($attemptSummary['submitted'] ?? 0) }}</span>
                <span class="badge text-bg-warning">In Progress: {{ (int) ($attemptSummary['in_progress'] ?? 0) }}</span>
                <span class="badge text-bg-secondary">Not Started: {{ (int) ($attemptSummary['not_started'] ?? 0) }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card mb-4">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Questions</h2>
                <a href="{{ route('doctor.questions.create', $exam->id) }}" class="btn btn-sm btn-primary">Add Question</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th class="text-center">Mark</th>
                        <th>Choices</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($exam->questions as $question)
                        <tr>
                            <td>{{ $question->order_no }}</td>
                            <td>{{ $question->question_text }}</td>
                            <td class="text-center">{{ number_format((float) $question->mark, 2) }}</td>
                            <td>
                                @foreach ($question->choices as $choice)
                                    <div class="small {{ $choice->is_correct ? 'text-success fw-semibold' : 'text-muted' }}">
                                        {{ $choice->choice_text }}
                                        @if ($choice->is_correct)
                                            <span class="badge text-bg-success ms-1">Correct</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('doctor.questions.edit', $question->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('doctor.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><div class="empty-state">No questions added yet.</div></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card table-card">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Latest Attempts</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Code</th>
                        <th>Status</th>
                        <th class="text-center">Obtained Marks</th>
                        <th class="text-center">Percentage</th>
                        <th>Submitted At</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($studentAttemptRows as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row['full_name'] }}</td>
                            <td>{{ $row['student_code'] }}</td>
                            <td>
                                <span class="badge text-bg-{{ $row['status_key'] === 'submitted' ? 'success' : ($row['status_key'] === 'in_progress' ? 'warning' : 'secondary') }}">
                                    {{ $row['status_label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($row['obtained_marks'] !== null && $row['total_marks'] !== null)
                                    {{ number_format((float) $row['obtained_marks'], 2) }} / {{ number_format((float) $row['total_marks'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $row['percentage'] !== null ? number_format((float) $row['percentage'], 2) : '-' }}</td>
                            <td>{{ $row['submitted_at']?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="text-end">
                                @if ($row['attempt_id'])
                                    <span class="badge text-bg-light">Attempt #{{ $row['attempt_id'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"><div class="empty-state">No students are enrolled in this subject yet.</div></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
