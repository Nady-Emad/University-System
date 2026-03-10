@extends('layouts.app')

@section('title', 'Edit Subject - University System')
@section('page-title', 'Edit Subject')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">Update Subject</h2>
    <form action="{{ route('subjects.update', $subject) }}" method="POST" novalidate>
        @method('PUT')
        @php($submitLabel = 'Update Subject')
        @include('subjects._form')
    </form>
</div>
@endsection
