@csrf

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $subject->code ?? '') }}" placeholder="CS101" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name ?? '') }}" placeholder="Programming 1" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Optional subject description">{{ old('description', $subject->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Credit Hours</label>
        <input type="number" name="credit_hours" class="form-control @error('credit_hours') is-invalid @enderror" value="{{ old('credit_hours', $subject->credit_hours ?? 3) }}" min="1" max="12" required>
        @error('credit_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
            @foreach (['Fall', 'Spring', 'Summer'] as $semester)
                <option value="{{ $semester }}" @selected(old('semester', $subject->semester ?? 'Spring') === $semester)>{{ $semester }}</option>
            @endforeach
        </select>
        @error('semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Academic Year</label>
        <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" value="{{ old('academic_year', $subject->academic_year ?? now()->year . '/' . (now()->year + 1)) }}" required>
        @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Assigned Doctors</label>
        <select name="doctor_ids[]" class="form-select @error('doctor_ids') is-invalid @enderror @error('doctor_ids.*') is-invalid @enderror" multiple size="6">
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected(in_array($doctor->id, old('doctor_ids', isset($subject) ? $subject->doctors->pluck('id')->all() : []), true))>
                    {{ $doctor->full_name }} ({{ $doctor->specialization }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Use Ctrl/Cmd to select multiple doctors.</div>
        @error('doctor_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @error('doctor_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Enrolled Students</label>
        <select name="student_ids[]" class="form-select @error('student_ids') is-invalid @enderror @error('student_ids.*') is-invalid @enderror" multiple size="6">
            @foreach ($students as $studentItem)
                <option value="{{ $studentItem->id }}" @selected(in_array($studentItem->id, old('student_ids', isset($subject) ? $subject->students->pluck('id')->all() : []), true))>
                    {{ $studentItem->full_name }} ({{ $studentItem->student_code }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Only selected students can access this subject's online exams.</div>
        @error('student_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @error('student_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
