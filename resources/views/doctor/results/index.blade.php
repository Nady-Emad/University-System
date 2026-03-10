@extends('layouts.doctor')

@section('title', 'My Results - Doctor Portal')
@section('page-title', 'Result Summaries (Midterm / Final)')

@section('content')
<div class="panel-card p-3 mb-4">
    <form action="{{ route('doctor.results') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Exam Type</label>
            <select name="exam_type" class="form-select">
                <option value="">All Types</option>
                <option value="midterm" @selected(request('exam_type') === 'midterm')>Midterm</option>
                <option value="final" @selected(request('exam_type') === 'final')>Final</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            <a href="{{ route('doctor.results') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="panel-card table-card mb-4">
    <div class="p-3 border-bottom">
        <h2 class="h6 mb-0">Entered Results</h2>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Student</th>
                <th>Exam</th>
                <th>Type</th>
                <th class="text-center">Marks</th>
                <th class="text-center">GP</th>
                <th class="text-center">CH</th>
                <th class="text-center">QP</th>
                <th class="text-end">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>{{ $result->student->full_name ?? '-' }}</td>
                    <td>
                        <div class="fw-semibold">{{ $result->exam->subject_name ?? '-' }}</div>
                        <div class="small text-muted">{{ $result->exam->title ?? '' }}</div>
                    </td>
                    <td>
                        @if ($result->exam)
                            <span class="exam-type-badge {{ $result->exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $result->exam->exam_type }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format((float) $result->marks, 2) }}</td>
                    <td class="text-center">{{ number_format((float) $result->grade_point, 2) }}</td>
                    <td class="text-center">{{ $result->credit_hours }}</td>
                    <td class="text-center">{{ number_format((float) $result->quality_points, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('doctor.results.edit', $result->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"><div class="empty-state">No results entered yet.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($results->hasPages())
        <div class="p-3 border-top">{{ $results->links() }}</div>
    @endif
</div>

<div class="panel-card table-card">
    <div class="p-3 border-bottom">
        <h2 class="h6 mb-0">Quick Entry by Exam</h2>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Exam</th>
                <th>Type</th>
                <th>Date</th>
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
                        <span class="exam-type-badge {{ $exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $exam->exam_type }}</span>
                    </td>
                    <td>{{ $exam->exam_date?->format('Y-m-d') }}</td>
                    <td class="text-end">
                        <a href="{{ route('doctor.results.create', $exam->id) }}" class="btn btn-sm btn-outline-primary">Enter Marks</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"><div class="empty-state">No assigned exams available.</div></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
