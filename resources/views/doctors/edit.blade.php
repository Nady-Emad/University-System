@extends('layouts.app')

@section('title', 'Edit Doctor - University System')
@section('page-title', 'Edit Doctor / Lecturer')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Update Doctor: {{ $doctor->full_name }}</h2>

    <form action="{{ route('doctors.update', $doctor) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        @include('doctors._form', ['doctor' => $doctor])

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Doctor</button>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
