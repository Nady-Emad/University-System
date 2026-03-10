@php
    $isEdit = isset($student);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="full_name" class="form-label">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $student->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" placeholder="Enter full name" required>
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $student->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" placeholder="student@university.com" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="01XXXXXXXXX">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="entry_year" class="form-label">Entry Year</label>
        <input type="number" id="entry_year" name="entry_year" value="{{ old('entry_year', $student->entry_year ?? now()->year) }}" class="form-control @error('entry_year') is-invalid @enderror" placeholder="2026" min="2000" max="{{ now()->year + 1 }}" required>
        @error('entry_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">{{ $isEdit ? 'New Password (Optional)' : 'Password' }}</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $isEdit ? 'Leave blank to keep existing' : 'Enter password' }}" {{ $isEdit ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" {{ $isEdit ? '' : 'required' }}>
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'graduated' => 'Graduated', 'suspended' => 'Suspended'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $student->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
