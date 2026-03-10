@extends('layouts.app')

@section('title', 'Add Subject - University System')
@section('page-title', 'Create Subject')

@section('content')
<div class="panel-card p-4">
    <h2 class="h6 mb-3">New Subject</h2>
    <form action="{{ route('subjects.store') }}" method="POST" novalidate>
        @php($submitLabel = 'Create Subject')
        @include('subjects._form')
    </form>
</div>
@endsection
