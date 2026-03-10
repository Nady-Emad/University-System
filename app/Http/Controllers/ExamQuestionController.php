<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\ExamQuestion;
use App\Models\OnlineExam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExamQuestionController extends Controller
{
    public function create(int $examId, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $exam = OnlineExam::query()
            ->with('subject')
            ->where('doctor_id', $doctor->id)
            ->findOrFail($examId);

        return view('doctor.online-exams.questions.create', compact('doctor', 'exam'));
    }

    public function store(int $examId, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $exam = OnlineExam::query()
            ->where('doctor_id', $doctor->id)
            ->findOrFail($examId);

        $validated = $this->validateQuestionPayload($request);

        DB::transaction(function () use ($exam, $validated): void {
            $question = $exam->questions()->create([
                'question_text' => $validated['question_text'],
                'mark' => $validated['mark'],
                'order_no' => $validated['order_no'],
            ]);

            foreach ($validated['choices'] as $index => $choiceText) {
                $question->choices()->create([
                    'choice_text' => $choiceText,
                    'is_correct' => (int) $validated['correct_choice'] === $index,
                ]);
            }

            $exam->update([
                'total_marks' => $exam->questions()->sum('mark'),
            ]);
        });

        return redirect()->route('doctor.online-exams.show', $exam->id)
            ->with('success', 'Question added successfully.');
    }

    public function edit(int $id, Request $request): View
    {
        $doctor = $this->resolveDoctor($request);

        $question = ExamQuestion::query()
            ->with(['choices', 'exam.subject'])
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->findOrFail($id);

        return view('doctor.online-exams.questions.edit', compact('doctor', 'question'));
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $question = ExamQuestion::query()
            ->with(['choices', 'exam'])
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->findOrFail($id);

        $validated = $this->validateQuestionPayload($request);

        DB::transaction(function () use ($question, $validated): void {
            $question->update([
                'question_text' => $validated['question_text'],
                'mark' => $validated['mark'],
                'order_no' => $validated['order_no'],
            ]);

            $question->choices()->delete();

            foreach ($validated['choices'] as $index => $choiceText) {
                $question->choices()->create([
                    'choice_text' => $choiceText,
                    'is_correct' => (int) $validated['correct_choice'] === $index,
                ]);
            }

            $question->exam->update([
                'total_marks' => $question->exam->questions()->sum('mark'),
            ]);
        });

        return redirect()->route('doctor.online-exams.show', $question->exam_id)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $doctor = $this->resolveDoctor($request);

        $question = ExamQuestion::query()
            ->with('exam')
            ->whereHas('exam', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->findOrFail($id);

        $exam = $question->exam;

        $question->delete();

        $exam->update([
            'total_marks' => $exam->questions()->sum('mark'),
        ]);

        return redirect()->route('doctor.online-exams.show', $exam->id)
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateQuestionPayload(Request $request): array
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'mark' => ['required', 'numeric', 'min:0.25', 'max:1000'],
            'order_no' => ['required', 'integer', 'min:1'],
            'choices' => ['required', 'array', 'min:2', 'max:6'],
            'choices.*' => ['required', 'string', 'max:500', 'distinct'],
            'correct_choice' => ['required', 'integer', Rule::in([0, 1, 2, 3, 4, 5])],
        ]);

        $choicesCount = count($validated['choices']);
        $correctChoice = (int) $validated['correct_choice'];

        if ($correctChoice < 0 || $correctChoice >= $choicesCount) {
            throw ValidationException::withMessages([
                'correct_choice' => 'Correct answer must match one of the listed choices.',
            ]);
        }

        return $validated;
    }

    private function resolveDoctor(Request $request): Doctor
    {
        $doctor = $request->user()?->doctor;

        abort_if(! $doctor, 403, 'Doctor profile was not found for this account.');

        return $doctor;
    }
}
