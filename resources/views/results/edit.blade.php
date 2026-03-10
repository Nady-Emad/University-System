@extends('layouts.app')

@section('title', 'Edit Result - University System')
@section('page-title', 'Edit Student Result')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Update Result</h2>

    <form action="{{ route('results.update', $result) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        @include('results._form', ['result' => $result])

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Result</button>
            <a href="{{ route('results.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
