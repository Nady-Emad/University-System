@extends('layouts.doctor')

@section('title', 'Add Question - Doctor Portal')
@section('page-title', 'Add Exam Question')

@section('content')
<div class="panel-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h6 mb-1">{{ $exam->title }}</h2>
            <div class="small text-muted">{{ $exam->subject->code }} - {{ $exam->subject->name }}</div>
        </div>
        <a href="{{ route('doctor.online-exams.show', $exam->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <form action="{{ route('doctor.questions.store', $exam->id) }}" method="POST" id="questionForm" novalidate>
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Question Text</label>
                <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="3" required>{{ old('question_text') }}</textarea>
                @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Mark</label>
                <input type="number" name="mark" step="0.25" min="0.25" class="form-control @error('mark') is-invalid @enderror" value="{{ old('mark', 1) }}" required>
                @error('mark')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Order Number</label>
                <input type="number" name="order_no" min="1" class="form-control @error('order_no') is-invalid @enderror" value="{{ old('order_no', 1) }}" required>
                @error('order_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label mb-0">Choices</label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addChoiceBtn">Add Choice</button>
                </div>

                <div id="choicesContainer" class="d-flex flex-column gap-2"></div>
                @error('choices')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('choices.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('correct_choice')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Save Question</button>
                <a href="{{ route('doctor.online-exams.show', $exam->id) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const container = document.getElementById('choicesContainer');
    const addButton = document.getElementById('addChoiceBtn');

    const oldChoices = @json(old('choices', ['','']));
    const oldCorrect = Number(@json(old('correct_choice', 0)));

    const render = () => {
        const rows = Array.from(container.querySelectorAll('[data-choice-row]'));
        rows.forEach((row, index) => {
            row.querySelector('[data-choice-label]').textContent = `Choice ${index + 1}`;
            row.querySelector('[data-choice-input]').name = 'choices[]';
            row.querySelector('[data-correct-input]').name = 'correct_choice';
            row.querySelector('[data-correct-input]').value = index;
        });
    };

    const addChoice = (value = '', isChecked = false) => {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center';
        row.dataset.choiceRow = '1';
        row.innerHTML = `
            <div class="col-1 text-muted small" data-choice-label></div>
            <div class="col-9">
                <input type="text" class="form-control" data-choice-input value="${value.replace(/"/g, '&quot;')}" required>
            </div>
            <div class="col-1 text-center">
                <input type="radio" class="form-check-input" data-correct-input ${isChecked ? 'checked' : ''}>
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-btn>&times;</button>
            </div>
        `;

        row.querySelector('[data-remove-btn]').addEventListener('click', () => {
            if (container.children.length <= 2) {
                return;
            }
            row.remove();
            render();
        });

        container.appendChild(row);
        render();
    };

    addButton.addEventListener('click', () => addChoice('', false));

    if (oldChoices.length) {
        oldChoices.forEach((choice, index) => addChoice(choice ?? '', index === oldCorrect));
    } else {
        addChoice('', true);
        addChoice('', false);
    }
})();
</script>
@endpush
