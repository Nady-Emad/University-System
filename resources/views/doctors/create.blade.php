@extends('layouts.app')

@section('title', 'Add Doctor - University System')
@section('page-title', 'Add Doctor / Lecturer')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Create Doctor / Lecturer</h2>

    <form action="{{ route('doctors.store') }}" method="POST" novalidate>
        @csrf

        @include('doctors._form')

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Doctor</button>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
