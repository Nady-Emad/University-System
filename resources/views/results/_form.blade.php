<div class="row g-3">
    <div class="col-md-6">
        <label for="student_id" class="form-label">Student</label>
        <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
            <option value="">Select student</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected((string) old('student_id', $result->student_id ?? '') === (string) $student->id)>
                    {{ $student->full_name }} ({{ $student->student_code }})
                </option>
            @endforeach
        </select>
        @error('student_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="exam_id" class="form-label">Exam</label>
        <select id="exam_id" name="exam_id" class="form-select @error('exam_id') is-invalid @enderror" required>
            <option value="">Select exam</option>
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}" data-total="{{ $exam->total_marks }}" data-credit-hours="{{ $exam->credit_hours }}" data-semester="{{ $exam->semester }}" data-academic-year="{{ $exam->academic_year }}" @selected((string) old('exam_id', $result->exam_id ?? '') === (string) $exam->id)>
                    {{ $exam->subject_name }} - {{ $exam->title }} ({{ ucfirst($exam->exam_type) }})
                </option>
            @endforeach
        </select>
        @error('exam_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="marks" class="form-label">Marks</label>
        <input type="number" step="0.01" id="marks" name="marks" value="{{ old('marks', $result->marks ?? '') }}" class="form-control @error('marks') is-invalid @enderror" min="0" required>
        @error('marks')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text" id="marksHelp">Select an exam to see total marks.</div>
    </div>

    <div class="col-md-4">
        <label for="credit_hours" class="form-label">Credit Hours</label>
        <input type="number" id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $result->credit_hours ?? '') }}" class="form-control @error('credit_hours') is-invalid @enderror" min="1" max="12" placeholder="Auto from exam if blank">
        @error('credit_hours')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester" class="form-select @error('semester') is-invalid @enderror">
            <option value="">Auto from exam</option>
            @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                <option value="{{ $semester }}" @selected(old('semester', $result->semester ?? '') === $semester)>{{ $semester }}</option>
            @endforeach
        </select>
        @error('semester')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="academic_year" class="form-label">Academic Year</label>
        <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $result->academic_year ?? '') }}" class="form-control @error('academic_year') is-invalid @enderror" placeholder="Auto from exam if blank">
        @error('academic_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const examInput = document.getElementById('exam_id');
        const creditHoursInput = document.getElementById('credit_hours');
        const semesterInput = document.getElementById('semester');
        const academicYearInput = document.getElementById('academic_year');
        const marksHelp = document.getElementById('marksHelp');

        function applyExamMetadata() {
            const selected = examInput.options[examInput.selectedIndex];
            if (!selected || !selected.value) {
                marksHelp.textContent = 'Select an exam to see total marks.';
                return;
            }

            marksHelp.textContent = `Exam total marks: ${selected.dataset.total}`;

            if (!creditHoursInput.value) {
                creditHoursInput.value = selected.dataset.creditHours || '';
            }

            if (!semesterInput.value) {
                semesterInput.value = selected.dataset.semester || '';
            }

            if (!academicYearInput.value) {
                academicYearInput.value = selected.dataset.academicYear || '';
            }
        }

        examInput.addEventListener('change', applyExamMetadata);
        applyExamMetadata();
    })();
</script>
@endpush
