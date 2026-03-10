@extends('layouts.student')

@section('title', 'My Exams - University System')

@section('content')
<div class="panel-card table-card mb-4">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h1 class="h6 mb-0">Traditional Exams</h1>
        <small class="text-muted">You can only view your own exam records</small>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Type</th>
                <th>Date</th>
                <th>CH</th>
                <th>Semester</th>
                <th>Academic Year</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($exams as $exam)
                @php
                    $myResult = $exam->results->first();
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $exam->subject_name }}</div>
                        <div class="small text-muted">{{ $exam->title }}</div>
                    </td>
                    <td>
                        <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $exam->exam_type }}</span>
                    </td>
                    <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                    <td>{{ $exam->credit_hours }}</td>
                    <td>{{ $exam->semester }}</td>
                    <td>{{ $exam->academic_year }}</td>
                    <td>
                        @if ($myResult)
                            <span class="badge text-bg-success">Result Published</span>
                        @else
                            <span class="badge text-bg-warning">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">No exams are available yet.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($exams->hasPages())
        <div class="p-3 border-top">{{ $exams->links() }}</div>
    @endif
</div>

<div class="panel-card table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h2 class="h6 mb-0">Real Online Exams</h2>
        <a href="{{ route('student.online-exams.index') }}" class="btn btn-sm btn-outline-primary">Open Exam Portal</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Subject</th>
                <th>Window</th>
                <th>Status</th>
                <th>My Attempt</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($onlineExams as $onlineExam)
                @php
                    $attempt = ($onlineAttempts[$onlineExam->id] ?? collect())->sortByDesc('created_at')->first();
                    $now = now();
                    $isLive = $onlineExam->status === 'published' && $now->between($onlineExam->start_time, $onlineExam->end_time);
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $onlineExam->title }}</div>
                        <div class="small text-muted text-capitalize">{{ $onlineExam->exam_type }}</div>
                    </td>
                    <td>{{ $onlineExam->subject->code ?? '-' }} - {{ $onlineExam->subject->name ?? '-' }}</td>
                    <td>
                        <div>{{ $onlineExam->start_time?->format('Y-m-d H:i') }}</div>
                        <div class="small text-muted">to {{ $onlineExam->end_time?->format('Y-m-d H:i') }}</div>
                    </td>
                    <td>
                        @if ($isLive)
                            <span class="badge text-bg-success">Live</span>
                        @else
                            <span class="badge text-bg-secondary text-capitalize">{{ $onlineExam->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($attempt)
                            <span class="badge text-bg-{{ in_array($attempt->status, ['submitted', 'auto_submitted']) ? 'success' : 'warning' }} text-capitalize">{{ str_replace('_', ' ', $attempt->status) }}</span>
                        @else
                            <span class="badge text-bg-light">No attempt</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('student.online-exams.show', $onlineExam->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">No online exams published yet for your enrolled subjects.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
