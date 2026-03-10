@extends('layouts.app')

@section('title', 'Edit Exam - University System')
@section('page-title', 'Edit Exam')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Update Exam: {{ $exam->subject_name }}</h2>

    <form action="{{ route('exams.update', $exam) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        @include('exams._form', ['exam' => $exam])

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Exam</button>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
