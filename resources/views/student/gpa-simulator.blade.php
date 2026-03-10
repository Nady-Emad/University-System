@extends('layouts.student')

@section('title', 'GPA Simulator - University System')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="panel-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h6 mb-0">GPA & CGPA Simulator (Pre-Exam Calculator)</h1>
                <button type="button" id="addSubject" class="btn btn-sm btn-primary">Add Subject</button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="simulatorTable">
                    <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th style="width: 130px;">Predicted Marks</th>
                        <th style="width: 120px;">Credit Hours</th>
                        <th style="width: 120px;">Grade Point</th>
                        <th style="width: 130px;">Quality Points</th>
                        <th style="width: 70px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="text" class="form-control subject-name" placeholder="Subject"></td>
                        <td><input type="number" class="form-control predicted-marks" min="0" max="100" step="0.01" placeholder="0 - 100"></td>
                        <td><input type="number" class="form-control credit-hours" min="1" max="12" step="1" value="3"></td>
                        <td class="grade-point fw-semibold text-center">0.00</td>
                        <td class="quality-points fw-semibold text-center">0.00</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row">X</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" id="calculateBtn" class="btn btn-outline-primary">Calculate GPA</button>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="panel-card p-3 mb-3">
            <h2 class="h6 mb-3">Current Academic Snapshot</h2>
            <input type="hidden" id="previousTotalQualityPoints" value="{{ number_format((float) $student->total_quality_points, 2, '.', '') }}">
            <input type="hidden" id="previousTotalCreditHours" value="{{ $student->total_completed_credit_hours }}">

            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Current GPA</span>
                <strong>{{ number_format((float) $student->current_gpa, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Current CGPA</span>
                <strong>{{ number_format((float) $student->current_cgpa, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-0">
                <span class="text-muted">Completed Credit Hours</span>
                <strong>{{ $student->total_completed_credit_hours }}</strong>
            </div>
        </div>

        <div class="panel-card p-3">
            <h2 class="h6 mb-3">Predicted Outcomes</h2>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <span>Predicted GPA</span>
                    <strong id="predictedGpaValue">0.00</strong>
                </div>
                <div class="progress mt-2">
                    <div id="predictedGpaBar" class="progress-bar bg-primary" style="width: 0%"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <span>Predicted CGPA</span>
                    <strong id="predictedCgpaValue">{{ number_format((float) $student->current_cgpa, 2) }}</strong>
                </div>
                <div class="progress mt-2">
                    <div id="predictedCgpaBar" class="progress-bar bg-success" style="width: {{ min(100, ((float) $student->current_cgpa / 4) * 100) }}%"></div>
                </div>
            </div>

            <div class="small text-muted">
                Formula: GPA = Total Quality Points / Total Credit Hours
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const tableBody = document.querySelector('#simulatorTable tbody');
        const addSubjectBtn = document.getElementById('addSubject');
        const calculateBtn = document.getElementById('calculateBtn');

        const predictedGpaValue = document.getElementById('predictedGpaValue');
        const predictedCgpaValue = document.getElementById('predictedCgpaValue');
        const predictedGpaBar = document.getElementById('predictedGpaBar');
        const predictedCgpaBar = document.getElementById('predictedCgpaBar');

        const previousTotalQualityPoints = parseFloat(document.getElementById('previousTotalQualityPoints').value) || 0;
        const previousTotalCreditHours = parseFloat(document.getElementById('previousTotalCreditHours').value) || 0;

        function gradePointFromMarks(marks) {
            if (marks >= 90) return 4.0;
            if (marks >= 85) return 3.7;
            if (marks >= 80) return 3.3;
            if (marks >= 75) return 3.0;
            if (marks >= 70) return 2.7;
            if (marks >= 65) return 2.3;
            if (marks >= 60) return 2.0;
            return 0.0;
        }

        function clampGpa(value) {
            return Math.max(0, Math.min(4, value));
        }

        function updateBars(gpa, cgpa) {
            const gpaPercent = (clampGpa(gpa) / 4) * 100;
            const cgpaPercent = (clampGpa(cgpa) / 4) * 100;

            predictedGpaBar.style.width = `${gpaPercent}%`;
            predictedCgpaBar.style.width = `${cgpaPercent}%`;
        }

        function recalculate() {
            let predictedQualityPoints = 0;
            let predictedCreditHours = 0;

            tableBody.querySelectorAll('tr').forEach((row) => {
                const marksInput = row.querySelector('.predicted-marks');
                const creditHoursInput = row.querySelector('.credit-hours');
                const gradePointCell = row.querySelector('.grade-point');
                const qualityPointsCell = row.querySelector('.quality-points');

                const marks = parseFloat(marksInput.value);
                const creditHours = parseFloat(creditHoursInput.value);

                if (!Number.isFinite(marks) || !Number.isFinite(creditHours) || creditHours <= 0) {
                    gradePointCell.textContent = '0.00';
                    qualityPointsCell.textContent = '0.00';
                    return;
                }

                const gradePoint = gradePointFromMarks(marks);
                const qualityPoints = gradePoint * creditHours;

                gradePointCell.textContent = gradePoint.toFixed(2);
                qualityPointsCell.textContent = qualityPoints.toFixed(2);

                predictedQualityPoints += qualityPoints;
                predictedCreditHours += creditHours;
            });

            const predictedGpa = predictedCreditHours > 0
                ? predictedQualityPoints / predictedCreditHours
                : 0;

            const predictedCgpa = (previousTotalCreditHours + predictedCreditHours) > 0
                ? (previousTotalQualityPoints + predictedQualityPoints) / (previousTotalCreditHours + predictedCreditHours)
                : 0;

            predictedGpaValue.textContent = predictedGpa.toFixed(2);
            predictedCgpaValue.textContent = predictedCgpa.toFixed(2);

            updateBars(predictedGpa, predictedCgpa);
        }

        function addRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control subject-name" placeholder="Subject"></td>
                <td><input type="number" class="form-control predicted-marks" min="0" max="100" step="0.01" placeholder="0 - 100"></td>
                <td><input type="number" class="form-control credit-hours" min="1" max="12" step="1" value="3"></td>
                <td class="grade-point fw-semibold text-center">0.00</td>
                <td class="quality-points fw-semibold text-center">0.00</td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-row">X</button></td>
            `;

            tableBody.appendChild(row);
        }

        tableBody.addEventListener('input', function (event) {
            if (event.target.classList.contains('predicted-marks') || event.target.classList.contains('credit-hours')) {
                recalculate();
            }
        });

        tableBody.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-row')) {
                if (tableBody.querySelectorAll('tr').length > 1) {
                    event.target.closest('tr').remove();
                    recalculate();
                }
            }
        });

        addSubjectBtn.addEventListener('click', function () {
            addRow();
        });

        calculateBtn.addEventListener('click', function () {
            recalculate();
        });

        recalculate();
    })();
</script>
@endpush
