<div class="row g-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Exam Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $exam->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Programming 1 Midterm" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="subject_name" class="form-label">Subject Name</label>
        <input type="text" id="subject_name" name="subject_name" value="{{ old('subject_name', $exam->subject_name ?? '') }}" class="form-control @error('subject_name') is-invalid @enderror" placeholder="Programming 1" required>
        @error('subject_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="exam_type" class="form-label">Exam Type</label>
        <select id="exam_type" name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
            <option value="midterm" @selected(old('exam_type', $exam->exam_type ?? 'midterm') === 'midterm')>Midterm</option>
            <option value="final" @selected(old('exam_type', $exam->exam_type ?? 'midterm') === 'final')>Final</option>
        </select>
        @error('exam_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="exam_date" class="form-label">Exam Date</label>
        <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date', isset($exam) ? $exam->exam_date?->format('Y-m-d') : '') }}" class="form-control @error('exam_date') is-invalid @enderror" required>
        @error('exam_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="total_marks" class="form-label">Total Marks</label>
        <input type="number" id="total_marks" name="total_marks" value="{{ old('total_marks', $exam->total_marks ?? 100) }}" class="form-control @error('total_marks') is-invalid @enderror" min="1" required>
        @error('total_marks')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="credit_hours" class="form-label">Credit Hours</label>
        <input type="number" id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $exam->credit_hours ?? 3) }}" class="form-control @error('credit_hours') is-invalid @enderror" min="1" max="12" required>
        @error('credit_hours')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester" class="form-select @error('semester') is-invalid @enderror" required>
            @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                <option value="{{ $semester }}" @selected(old('semester', $exam->semester ?? 'Fall') === $semester)>{{ $semester }}</option>
            @endforeach
        </select>
        @error('semester')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="academic_year" class="form-label">Academic Year</label>
        <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $exam->academic_year ?? now()->year . '/' . (now()->year + 1)) }}" class="form-control @error('academic_year') is-invalid @enderror" placeholder="2025/2026" required>
        @error('academic_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="doctor_id" class="form-label">Assigned Doctor</label>
        <select id="doctor_id" name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
            <option value="">Select doctor</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected((string) old('doctor_id', $exam->doctor_id ?? '') === (string) $doctor->id)>{{ $doctor->full_name }}</option>
            @endforeach
        </select>
        @error('doctor_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
