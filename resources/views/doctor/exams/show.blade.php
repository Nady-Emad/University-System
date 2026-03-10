@extends('layouts.doctor')

@section('title', 'Exam Details - Doctor Portal')
@section('page-title', 'Exam Details and Students')

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
                <dd class="col-7">{{ $exam->subject_name }}</dd>

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
            </dl>
        </div>

        <div class="panel-card p-4">
            <h2 class="h6 mb-3">Edit Exam</h2>
            <form action="{{ route('doctor.exams.update', $exam->id) }}" method="POST" class="row g-3" novalidate>
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $exam->title) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="subject_name" class="form-control" value="{{ old('subject_name', $exam->subject_name) }}" required>
                </div>

                <div class="col-6">
                    <label class="form-label">Type</label>
                    <select name="exam_type" class="form-select" required>
                        <option value="midterm" @selected(old('exam_type', $exam->exam_type) === 'midterm')>Midterm</option>
                        <option value="final" @selected(old('exam_type', $exam->exam_type) === 'final')>Final</option>
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="exam_date" class="form-control" value="{{ old('exam_date', $exam->exam_date?->format('Y-m-d')) }}" required>
                </div>

                <div class="col-6">
                    <label class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                </div>

                <div class="col-6">
                    <label class="form-label">Credit Hours</label>
                    <input type="number" name="credit_hours" class="form-control" value="{{ old('credit_hours', $exam->credit_hours) }}" min="1" max="12" required>
                </div>

                <div class="col-6">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                            <option value="{{ $semester }}" @selected(old('semester', $exam->semester) === $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $exam->academic_year) }}" required>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>

            <form action="{{ route('doctor.exams.destroy', $exam->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this exam and related results?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete Exam</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h6 mb-0">Students and Marks</h2>
                    <div class="small text-muted">All linked students are shown, including students without marks.</div>
                </div>
                <a href="{{ route('doctor.results.create', ['examId' => $exam->id]) }}" class="btn btn-sm btn-primary">Enter Marks</a>
            </div>

            @if (! $isSubjectLinked)
                <div class="alert alert-warning m-3 mb-0">
                    Subject mapping is missing for this exam. All students are shown until the subject is linked.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Status</th>
                        <th class="text-center">Marks</th>
                        <th class="text-center">GP</th>
                        <th class="text-center">QP</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($studentRows as $row)
                        @php
                            $student = $row['student'];
                            $result = $row['result'];
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $student->full_name }}</div>
                                <div class="small text-muted">{{ $student->student_code }}</div>
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $result ? 'success' : 'secondary' }}">
                                    {{ $result ? 'Marked' : 'Not Marked' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $result ? number_format((float) $result->marks, 2) : '-' }}</td>
                            <td class="text-center">{{ $result ? number_format((float) $result->grade_point, 2) : '-' }}</td>
                            <td class="text-center">{{ $result ? number_format((float) $result->quality_points, 2) : '-' }}</td>
                            <td class="text-end">
                                @if ($result)
                                    <a href="{{ route('doctor.results.edit', $result->id) }}" class="btn btn-sm btn-outline-primary">Edit Mark</a>
                                @else
                                    <a href="{{ route('doctor.results.create', ['examId' => $exam->id, 'student_id' => $student->id]) }}" class="btn btn-sm btn-outline-success">Add Mark</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="empty-state">No students linked to this exam yet.</div></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
