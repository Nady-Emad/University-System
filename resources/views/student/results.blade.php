@extends('layouts.student')

@section('title', 'My Results - University System')

@section('content')
<div class="alert alert-info">
    Midterm and Final are treated as components of the same subject. GPA/CGPA counts each subject once per term.
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="panel-card table-card h-100 mb-4">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h1 class="h6 mb-0">Exam Components (Details)</h1>
                <small class="text-muted">Only your own results are visible</small>
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
                        <th>Term</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($results as $result)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $result->exam->subject_name ?? '-' }}</div>
                                <div class="small text-muted">{{ $result->exam->title ?? '' }}</div>
                            </td>
                            <td>
                                @if ($result->exam)
                                    <span class="exam-type-badge exam-type-{{ $result->exam->exam_type }}">{{ $result->exam->exam_type }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format((float) $result->marks, 2) }}</td>
                            <td class="text-center">{{ number_format((float) $result->grade_point, 2) }}</td>
                            <td class="text-center">{{ number_format((float) $result->quality_points, 2) }}</td>
                            <td>{{ $result->semester }} / {{ $result->academic_year }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">No results have been published for your account.</div>
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
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">Course Summary (Used In GPA)</h2>
                <small class="text-muted">Merged by subject per term</small>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Subject</th>
                        <th class="text-center">Components</th>
                        <th class="text-center">Combined Mark</th>
                        <th class="text-center">CH</th>
                        <th class="text-center">GP</th>
                        <th class="text-center">QP</th>
                        <th>Term</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($subjectSummaries as $summary)
                        <tr>
                            <td class="fw-semibold">{{ $summary['subject_name'] }}</td>
                            <td class="text-center">{{ $summary['exam_components'] }}</td>
                            <td class="text-center">{{ number_format((float) $summary['combined_marks'], 2) }}</td>
                            <td class="text-center">{{ $summary['credit_hours'] }}</td>
                            <td class="text-center">{{ number_format((float) $summary['grade_point'], 2) }}</td>
                            <td class="text-center">{{ number_format((float) $summary['quality_points'], 2) }}</td>
                            <td>{{ $summary['semester'] }} / {{ $summary['academic_year'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">No merged course summaries yet.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel-card mb-3 p-3">
            <h2 class="h6">Current Performance</h2>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Current GPA</span>
                <strong>{{ number_format((float) $student->current_gpa, 2) }}</strong>
            </div>
            <div class="progress mb-3">
                <div class="progress-bar bg-primary" style="width: {{ min(100, ((float) $student->current_gpa / 4) * 100) }}%"></div>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Current CGPA</span>
                <strong>{{ number_format((float) $student->current_cgpa, 2) }}</strong>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" style="width: {{ min(100, ((float) $student->current_cgpa / 4) * 100) }}%"></div>
            </div>
        </div>

        <div class="panel-card table-card">
            <div class="p-3 border-bottom">
                <h2 class="h6 mb-0">Term GPA Summary</h2>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Term</th>
                        <th class="text-center">CH</th>
                        <th class="text-center">GPA</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($termSummaries as $summary)
                        <tr>
                            <td>{{ $summary['semester'] }} / {{ $summary['academic_year'] }}</td>
                            <td class="text-center">{{ $summary['total_credit_hours'] }}</td>
                            <td class="text-center fw-semibold">{{ number_format((float) $summary['gpa'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">No term summaries yet.</div>
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
