<?php

namespace App\Models;

use App\Support\AcademicCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class StudentExamAttempt extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'started_at',
        'submitted_at',
        'status',
        'obtained_marks',
        'total_marks',
        'percentage',
        'grade_point',
        'quality_points',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'obtained_marks' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'quality_points' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class, 'exam_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    public function deadline(): Carbon
    {
        $startedAt = $this->started_at ?? now();
        $durationEnd = (clone $startedAt)->addMinutes($this->exam?->duration_minutes ?? 0);

        return $durationEnd->lessThan($this->exam->end_time) ? $durationEnd : $this->exam->end_time;
    }

    public function recalculate(float $obtainedMarks): void
    {
        $totalMarks = (float) ($this->exam?->total_marks ?? 0.0);
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0.0;
        $gradePoint = AcademicCalculator::gradePointFromMarks($percentage);

        $creditHours = (int) ($this->exam?->subject?->credit_hours ?? 0);
        $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, $creditHours);

        $this->update([
            'obtained_marks' => round($obtainedMarks, 2),
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'grade_point' => $gradePoint,
            'quality_points' => $qualityPoints,
        ]);
    }
}
