@extends('layouts.app')

@section('title', 'Results - University System')
@section('page-title', 'Result & GPA/CGPA Management')

@section('content')
<div class="panel-card p-3 mb-4">
    <form method="GET" action="{{ route('results.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select">
                <option value="">All Students</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(request('student_id') == $student->id)>
                        {{ $student->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label">Exam Type</label>
            <select name="exam_type" class="form-select">
                <option value="">All</option>
                <option value="midterm" @selected(request('exam_type') === 'midterm')>Midterm</option>
                <option value="final" @selected(request('exam_type') === 'final')>Final</option>
            </select>
        </div>

        <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label">Semester</label>
            <select name="semester" class="form-select">
                <option value="">All</option>
                @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                    <option value="{{ $semester }}" @selected(request('semester') === $semester)>{{ $semester }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label">Academic Year</label>
            <input type="text" name="academic_year" value="{{ request('academic_year') }}" class="form-control" placeholder="2025/2026">
        </div>

        <div class="col-12 col-md-2 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            <a href="{{ route('results.index') }}" class="btn btn-outline-secondary">Reset</a>
            <a href="{{ route('results.create') }}" class="btn btn-primary ms-auto">Add Result</a>
        </div>
    </form>
</div>

<div class="panel-card table-card mb-4">
    <div class="p-3 border-bottom">
        <h2 class="h6 mb-0">Recorded Results</h2>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Student</th>
                <th>Exam</th>
                <th>Type</th>
                <th>Doctor</th>
                <th class="text-center">Marks</th>
                <th class="text-center">GP</th>
                <th class="text-center">CH</th>
                <th class="text-center">QP</th>
                <th>Term</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $result->student->full_name ?? '-' }}</div>
                        <div class="small text-muted">{{ $result->student->student_code ?? '' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $result->exam->subject_name ?? '-' }}</div>
                        <div class="small text-muted">{{ $result->exam->title ?? '' }}</div>
                    </td>
                    <td>
                        @if ($result->exam)
                            <span class="exam-type-badge {{ $result->exam->exam_type === 'midterm' ? 'exam-type-midterm' : 'exam-type-final' }}">{{ $result->exam->exam_type }}</span>
                        @endif
                    </td>
                    <td>{{ $result->exam->doctor->full_name ?? '-' }}</td>
                    <td class="text-center">{{ number_format((float) $result->marks, 2) }}</td>
                    <td class="text-center">{{ number_format((float) $result->grade_point, 2) }}</td>
                    <td class="text-center">{{ $result->credit_hours }}</td>
                    <td class="text-center">{{ number_format((float) $result->quality_points, 2) }}</td>
                    <td>{{ $result->semester }} / {{ $result->academic_year }}</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('results.show', $result) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('results.edit', $result) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('results.destroy', $result) }}" method="POST" onsubmit="return confirm('Delete this result?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">No results found. Add results to compute GPA/CGPA.</div>
                    </td>
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
        <h2 class="h6 mb-0">All Students GPA / CGPA Summary</h2>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Student</th>
                <th>Code</th>
                <th class="text-center">Current GPA</th>
                <th class="text-center">Current CGPA</th>
                <th class="text-center">Completed CH</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($gpaSummaries as $summary)
                <tr>
                    <td>{{ $summary->full_name }}</td>
                    <td>{{ $summary->student_code }}</td>
                    <td class="text-center">{{ number_format((float) $summary->current_gpa, 2) }}</td>
                    <td class="text-center fw-semibold">{{ number_format((float) $summary->current_cgpa, 2) }}</td>
                    <td class="text-center">{{ $summary->total_completed_credit_hours }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">No GPA/CGPA summaries available yet.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
