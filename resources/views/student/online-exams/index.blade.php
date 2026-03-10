@extends('layouts.student')

@section('title', 'Take Online Exams - University System')

@section('content')
<div class="panel-card table-card">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h1 class="h6 mb-0">Real Online Exams</h1>
        <small class="text-muted">Only enrolled subjects are shown</small>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Type</th>
                <th>Subject</th>
                <th>Window</th>
                <th>Status</th>
                <th>My Attempt</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($exams as $exam)
                @php
                    $latestAttempt = ($attempts[$exam->id] ?? collect())->sortByDesc('created_at')->first();
                    $now = now();
                    $isUpcoming = $now->lt($exam->start_time);
                    $isLive = $exam->status === 'published' && $now->between($exam->start_time, $exam->end_time);
                    $isClosed = $now->gt($exam->end_time) || $exam->status === 'closed';
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $exam->title }}</td>
                    <td><span class="exam-type-badge exam-type-{{ $exam->exam_type }}">{{ $exam->exam_type }}</span></td>
                    <td>{{ $exam->subject->code ?? '-' }} - {{ $exam->subject->name ?? '-' }}</td>
                    <td>
                        <div>{{ $exam->start_time?->format('Y-m-d H:i') }}</div>
                        <div class="small text-muted">to {{ $exam->end_time?->format('Y-m-d H:i') }}</div>
                    </td>
                    <td>
                        @if ($isLive)
                            <span class="badge text-bg-success">Live</span>
                        @elseif ($isUpcoming)
                            <span class="badge text-bg-warning">Upcoming</span>
                        @elseif ($isClosed)
                            <span class="badge text-bg-secondary">Closed</span>
                        @else
                            <span class="badge text-bg-secondary text-capitalize">{{ $exam->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($latestAttempt)
                            <span class="badge text-bg-{{ in_array($latestAttempt->status, ['submitted', 'auto_submitted']) ? 'success' : 'warning' }} text-capitalize">
                                {{ str_replace('_', ' ', $latestAttempt->status) }}
                            </span>
                            @if (in_array($latestAttempt->status, ['submitted', 'auto_submitted']))
                                <div class="small text-muted mt-1">Score: {{ number_format((float) $latestAttempt->percentage, 2) }}%</div>
                            @endif
                        @else
                            <span class="badge text-bg-light">No attempt</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('student.online-exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">
                            @if ($latestAttempt && in_array($latestAttempt->status, ['submitted', 'auto_submitted']) && ! $exam->allow_retake)
                                View Result
                            @elseif ($latestAttempt && $latestAttempt->status === 'in_progress')
                                Continue
                            @else
                                Start Exam
                            @endif
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7"><div class="empty-state">No online exams are currently available for your enrolled subjects.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

