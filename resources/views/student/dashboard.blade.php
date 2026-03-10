@extends('layouts.student')

@section('title', 'Student Dashboard - University System')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Current GPA</div>
            <p class="stat-value">{{ number_format((float) $student->current_gpa, 2) }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Current CGPA</div>
            <p class="stat-value">{{ number_format((float) $student->current_cgpa, 2) }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Completed Credit Hours</div>
            <p class="stat-value">{{ $student->total_completed_credit_hours }}</p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-title">Total Quality Points</div>
            <p class="stat-value">{{ number_format((float) $student->total_quality_points, 2) }}</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-title">Available Online Exams</div>
            <p class="stat-value fs-4">{{ $onlineExamStats['available'] }}</p>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-title">In Progress Attempts</div>
            <p class="stat-value fs-4">{{ $onlineExamStats['in_progress'] }}</p>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-title">Completed Online Attempts</div>
            <p class="stat-value fs-4">{{ $onlineExamStats['completed'] }}</p>
        </div>
    </div>
</div>

@if ($nextOnlineExam)
<div class="panel-card p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
    <div>
        <div class="small text-muted">Next Online Exam</div>
        <div class="fw-semibold">{{ $nextOnlineExam->title }}</div>
        <div class="small text-muted">{{ $nextOnlineExam->subject->code }} - {{ $nextOnlineExam->subject->name }} | {{ $nextOnlineExam->start_time?->format('Y-m-d H:i') }}</div>
    </div>
    <a href="{{ route('student.online-exams.show', $nextOnlineExam->id) }}" class="btn btn-sm btn-primary">Open</a>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Recent Results</h2>
                <a href="{{ route('student.results') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Type</th>
                        <th class="text-center">Marks</th>
                        <th class="text-center">GP</th>
                        <th class="text-center">QP</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentResults as $result)
                        <tr>
                            <td>{{ $result->exam->subject_name ?? '-' }}</td>
                            <td>
                                @if ($result->exam)
                                    <span class="exam-type-badge {{ $result->exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $result->exam->exam_type }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format((float) $result->marks, 2) }}</td>
                            <td class="text-center">{{ number_format((float) $result->grade_point, 2) }}</td>
                            <td class="text-center">{{ number_format((float) $result->quality_points, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No results are available yet.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Upcoming Traditional Exams</h2>
                <a href="{{ route('student.exams') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($upcomingExams as $exam)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $exam->subject_name }}</div>
                                <div class="small text-muted">{{ $exam->title }}</div>
                            </td>
                            <td>
                                <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $exam->exam_type }}</span>
                            </td>
                            <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">No upcoming exams scheduled.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
