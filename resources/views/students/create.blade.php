@extends('layouts.app')

@section('title', 'Add Student - University System')
@section('page-title', 'Add Student')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Create Student</h2>

    <form action="{{ route('students.store') }}" method="POST" novalidate>
        @csrf

        @include('students._form')

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Student</button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
