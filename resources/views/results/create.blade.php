@extends('layouts.app')

@section('title', 'Add Result - University System')
@section('page-title', 'Add Student Result')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Create Result Record</h2>

    <form action="{{ route('results.store') }}" method="POST" novalidate>
        @csrf

        @include('results._form')

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Result</button>
            <a href="{{ route('results.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
