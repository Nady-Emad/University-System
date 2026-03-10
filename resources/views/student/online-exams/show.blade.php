@extends('layouts.student')

@section('title', 'Online Exam - University System')

@section('content')
<div class="panel-card p-4 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <h1 class="h5 mb-1">{{ $exam->title }}</h1>
            <div class="small text-muted">
                {{ $exam->subject->code ?? '-' }} - {{ $exam->subject->name ?? '-' }}
                | {{ ucfirst($exam->exam_type) }}
                | Duration: {{ $exam->duration_minutes }} min
            </div>
        </div>

        <div class="text-lg-end">
            @if ($isReadOnly)
                <span class="badge text-bg-success">Submitted</span>
                <div class="small text-muted mt-1">Submitted at: {{ $attempt->submitted_at?->format('Y-m-d H:i') }}</div>
            @else
                <div class="small text-muted">Time Remaining</div>
                <div class="h5 mb-0 text-danger" id="countdown">--:--</div>
            @endif
        </div>
    </div>
</div>

@if ($isReadOnly)
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-title">Obtained</div>
                <p class="stat-value fs-4">{{ number_format((float) $attempt->obtained_marks, 2) }}</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-title">Total Marks</div>
                <p class="stat-value fs-4">{{ number_format((float) $attempt->total_marks, 2) }}</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-title">Percentage</div>
                <p class="stat-value fs-4">{{ number_format((float) $attempt->percentage, 2) }}%</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-title">Grade Point</div>
                <p class="stat-value fs-4">{{ number_format((float) $attempt->grade_point, 2) }}</p>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('student.online-exams.submit', $exam->id) }}" method="POST" id="examForm" novalidate>
    @csrf

    @foreach ($exam->questions as $question)
        @php
            $savedAnswer = $answersByQuestion->get($question->id);
            $selectedChoiceId = old('answers.' . $question->id, $savedAnswer?->selected_choice_id);
        @endphp

        <div class="panel-card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <h2 class="h6 mb-0">Q{{ $loop->iteration }}. {{ $question->question_text }}</h2>
                <span class="badge text-bg-light">{{ number_format((float) $question->mark, 2) }} marks</span>
            </div>

            <div class="d-flex flex-column gap-2">
                @foreach ($question->choices as $choice)
                    <label class="border rounded-3 p-2 d-flex align-items-center gap-2 {{ $isReadOnly && $choice->is_correct ? 'border-success bg-success-subtle' : '' }}">
                        <input
                            type="radio"
                            name="answers[{{ $question->id }}]"
                            value="{{ $choice->id }}"
                            @checked((string) $selectedChoiceId === (string) $choice->id)
                            @disabled($isReadOnly)
                        >
                        <span>{{ $choice->choice_text }}</span>
                        @if ($isReadOnly && $choice->is_correct)
                            <span class="badge text-bg-success ms-auto">Correct</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="d-flex gap-2">
        @if (! $isReadOnly)
            <button class="btn btn-primary" type="submit">Submit Exam</button>
        @endif
        <a href="{{ route('student.online-exams.index') }}" class="btn btn-outline-secondary">Back to Exams</a>
    </div>
</form>
@endsection

@push('scripts')
@if (! $isReadOnly)
<script>
(() => {
    const countdownNode = document.getElementById('countdown');
    const form = document.getElementById('examForm');
    let remaining = Number(@json($remainingSeconds));

    const format = (totalSeconds) => {
        const hrs = Math.floor(totalSeconds / 3600);
        const mins = Math.floor((totalSeconds % 3600) / 60);
        const secs = totalSeconds % 60;

        const hh = String(hrs).padStart(2, '0');
        const mm = String(mins).padStart(2, '0');
        const ss = String(secs).padStart(2, '0');

        return `${hh}:${mm}:${ss}`;
    };

    const tick = () => {
        if (remaining <= 0) {
            countdownNode.textContent = '00:00:00';
            form.submit();
            return;
        }

        countdownNode.textContent = format(remaining);
        remaining -= 1;
    };

    tick();
    setInterval(tick, 1000);
})();
</script>
@endif
@endpush
