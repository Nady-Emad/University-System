@extends('layouts.app')

@section('title', 'Exam Details - University System')
@section('page-title', 'Exam Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card p-4 h-100">
            <h2 class="h6 mb-3">Exam Information</h2>
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Title</dt>
                <dd class="col-7">{{ $exam->title }}</dd>

                <dt class="col-5 text-muted">Subject</dt>
                <dd class="col-7">{{ $exam->subject_name }}</dd>

                <dt class="col-5 text-muted">Type</dt>
                <dd class="col-7">
                    <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $exam->exam_type }}</span>
                </dd>

                <dt class="col-5 text-muted">Date</dt>
                <dd class="col-7">{{ $exam->exam_date?->format('Y-m-d') }}</dd>

                <dt class="col-5 text-muted">Total Marks</dt>
                <dd class="col-7">{{ $exam->total_marks }}</dd>

                <dt class="col-5 text-muted">Credit Hours</dt>
                <dd class="col-7">{{ $exam->credit_hours }}</dd>

                <dt class="col-5 text-muted">Semester</dt>
                <dd class="col-7">{{ $exam->semester }}</dd>

                <dt class="col-5 text-muted">Academic Year</dt>
                <dd class="col-7">{{ $exam->academic_year }}</dd>

                <dt class="col-5 text-muted">Doctor</dt>
                <dd class="col-7">{{ $exam->doctor->full_name ?? '-' }}</dd>
            </dl>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Student Results For This Exam</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Marks</th>
                        <th>Grade Point</th>
                        <th>CH</th>
                        <th>QP</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($exam->results as $result)
                        <tr>
                            <td>{{ $result->student->full_name ?? '-' }}</td>
                            <td>{{ number_format((float) $result->marks, 2) }}</td>
                            <td>{{ number_format((float) $result->grade_point, 2) }}</td>
                            <td>{{ $result->credit_hours }}</td>
                            <td>{{ number_format((float) $result->quality_points, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No results recorded for this exam yet.</div>
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
