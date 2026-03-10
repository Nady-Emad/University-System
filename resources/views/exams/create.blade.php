@extends('layouts.app')

@section('title', 'Add Exam - University System')
@section('page-title', 'Add Exam')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Create Exam</h2>

    <form action="{{ route('exams.store') }}" method="POST" novalidate>
        @csrf

        @include('exams._form')

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Exam</button>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
