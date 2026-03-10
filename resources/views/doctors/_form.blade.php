@php
    $isEdit = isset($doctor);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="full_name" class="form-label">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $doctor->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" placeholder="Enter full name" required>
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $doctor->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" placeholder="doctor@university.com" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $doctor->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="01XXXXXXXXX">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="specialization" class="form-label">Specialization</label>
        <input type="text" id="specialization" name="specialization" value="{{ old('specialization', $doctor->specialization ?? '') }}" class="form-control @error('specialization') is-invalid @enderror" placeholder="Computer Science" required>
        @error('specialization')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">{{ $isEdit ? 'New Password (Optional)' : 'Password' }}</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $isEdit ? 'Leave blank to keep current password' : 'Enter password' }}" {{ $isEdit ? '' : 'required' }}>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" {{ $isEdit ? '' : 'required' }}>
    </div>
</div>
