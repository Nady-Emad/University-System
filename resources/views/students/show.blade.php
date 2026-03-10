@extends('layouts.app')

@section('title', 'Student Details - University System')
@section('page-title', 'Student Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card p-4 h-100">
            <h2 class="h6 mb-3">Profile</h2>
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Full Name</dt>
                <dd class="col-7">{{ $student->full_name }}</dd>

                <dt class="col-5 text-muted">Email</dt>
                <dd class="col-7">{{ $student->email }}</dd>

                <dt class="col-5 text-muted">Phone</dt>
                <dd class="col-7">{{ $student->phone ?: '-' }}</dd>

                <dt class="col-5 text-muted">Entry Year</dt>
                <dd class="col-7">{{ $student->entry_year }}</dd>

                <dt class="col-5 text-muted">Student Code</dt>
                <dd class="col-7">{{ $student->student_code }}</dd>

                <dt class="col-5 text-muted">Status</dt>
                <dd class="col-7 text-capitalize">{{ $student->status }}</dd>

                <dt class="col-5 text-muted">Current GPA</dt>
                <dd class="col-7">{{ number_format((float) $student->current_gpa, 2) }}</dd>

                <dt class="col-5 text-muted">Current CGPA</dt>
                <dd class="col-7">{{ number_format((float) $student->current_cgpa, 2) }}</dd>
            </dl>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Academic Results</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Grade Point</th>
                        <th>Credit Hours</th>
                        <th>Quality Points</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($student->results as $result)
                        <tr>
                            <td>{{ $result->exam->subject_name ?? '-' }}</td>
                            <td>{{ number_format((float) $result->marks, 2) }}</td>
                            <td>{{ number_format((float) $result->grade_point, 2) }}</td>
                            <td>{{ $result->credit_hours }}</td>
                            <td>{{ number_format((float) $result->quality_points, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No results found for this student.</div>
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
