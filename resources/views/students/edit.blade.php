@extends('layouts.app')

@section('title', 'Edit Student - University System')
@section('page-title', 'Edit Student')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Update Student: {{ $student->full_name }}</h2>

    <form action="{{ route('students.update', $student) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        @include('students._form', ['student' => $student])

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Student</button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
