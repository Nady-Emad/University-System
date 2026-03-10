@extends('layouts.app')

@section('title', 'Subject Details - University System')
@section('page-title', 'Subject Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card p-4 h-100">
            <h2 class="h6 mb-3">Subject Information</h2>
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Code</dt>
                <dd class="col-7">{{ $subject->code }}</dd>

                <dt class="col-5 text-muted">Name</dt>
                <dd class="col-7">{{ $subject->name }}</dd>

                <dt class="col-5 text-muted">Credit Hours</dt>
                <dd class="col-7">{{ $subject->credit_hours }}</dd>

                <dt class="col-5 text-muted">Semester</dt>
                <dd class="col-7">{{ $subject->semester }}</dd>

                <dt class="col-5 text-muted">Academic Year</dt>
                <dd class="col-7">{{ $subject->academic_year }}</dd>
            </dl>

            <hr>
            <p class="mb-0 text-muted">{{ $subject->description ?: 'No description available.' }}</p>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="panel-card table-card mb-4">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Assigned Doctors</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Specialization</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($subject->doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->full_name }}</td>
                            <td>{{ $doctor->specialization }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2"><div class="empty-state">No doctors assigned yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card table-card mb-4">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Enrolled Students</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Student</th>
                        <th>Code</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($subject->students as $student)
                        <tr>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->student_code }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2"><div class="empty-state">No students enrolled yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card table-card">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Online Exams</h2>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Doctor</th>
                        <th>Time Window</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($subject->onlineExams as $exam)
                        <tr>
                            <td>{{ $exam->title }}</td>
                            <td><span class="exam-type-badge exam-type-{{ $exam->exam_type }}">{{ $exam->exam_type }}</span></td>
                            <td><span class="badge text-bg-secondary text-capitalize">{{ $exam->status }}</span></td>
                            <td>{{ $exam->doctor->full_name ?? '-' }}</td>
                            <td>{{ $exam->start_time?->format('Y-m-d H:i') }} - {{ $exam->end_time?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No online exams created for this subject yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

