<?php

namespace App\Models;

use App\Support\AcademicCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Student extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'entry_year',
        'student_code',
        'status',
        'current_gpa',
        'current_cgpa',
        'total_completed_credit_hours',
        'total_quality_points',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_year' => 'integer',
            'current_gpa' => 'decimal:2',
            'current_cgpa' => 'decimal:2',
            'total_completed_credit_hours' => 'integer',
            'total_quality_points' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject')
            ->withPivot('enrollment_status')
            ->withTimestamps();
    }

    public function onlineExamAttempts(): HasMany
    {
        return $this->hasMany(StudentExamAttempt::class);
    }

    /**
     * Build GPA components by subject (not by exam).
     * Midterm + Final for the same subject/term are merged into one course record.
     *
     * @return Collection<int, array<string, int|float|string>>
     */
    public function subjectPerformanceSummaries(): Collection
    {
        $results = $this->results()
            ->with('exam:id,subject_name,total_marks')
            ->get();

        return $results
            ->groupBy(function (Result $result): string {
                $subjectName = strtolower(trim((string) ($result->exam?->subject_name ?? ('exam-' . $result->exam_id))));

                return $subjectName . '|' . $result->semester . '|' . $result->academic_year;
            })
            ->map(function (Collection $group): array {
                /** @var Result $first */
                $first = $group->first();

                $creditHours = (int) $group->max(fn (Result $result) => (int) $result->credit_hours);

                $totalObtained = (float) $group->sum(fn (Result $result) => (float) $result->marks);
                $totalPossible = (float) $group->sum(function (Result $result): float {
                    $examTotal = (float) ($result->exam?->total_marks ?? 100);

                    return $examTotal > 0 ? $examTotal : 100.0;
                });

                $combinedMarks = $totalPossible > 0
                    ? round(($totalObtained / $totalPossible) * 100, 2)
                    : 0.0;

                $gradePoint = AcademicCalculator::gradePointFromMarks($combinedMarks);
                $qualityPoints = AcademicCalculator::qualityPoints($gradePoint, $creditHours);

                return [
                    'subject_name' => (string) ($first->exam?->subject_name ?? 'Unknown Subject'),
                    'semester' => (string) $first->semester,
                    'academic_year' => (string) $first->academic_year,
                    'credit_hours' => $creditHours,
                    'combined_marks' => $combinedMarks,
                    'grade_point' => $gradePoint,
                    'quality_points' => $qualityPoints,
                    'exam_components' => $group->count(),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, array<string, int|float|string>>
     */
    public function termPerformanceSummaries(): Collection
    {
        $semesterOrder = ['Fall' => 1, 'Spring' => 2, 'Summer' => 3];

        return $this->subjectPerformanceSummaries()
            ->groupBy(fn (array $item): string => $item['academic_year'] . '|' . $item['semester'])
            ->map(function (Collection $group): array {
                $first = $group->first();
                $totalCreditHours = (int) $group->sum(fn (array $item): int => (int) $item['credit_hours']);
                $totalQualityPoints = (float) $group->sum(fn (array $item): float => (float) $item['quality_points']);

                return [
                    'academic_year' => (string) $first['academic_year'],
                    'semester' => (string) $first['semester'],
                    'total_credit_hours' => $totalCreditHours,
                    'total_quality_points' => round($totalQualityPoints, 2),
                    'gpa' => AcademicCalculator::gpa($totalQualityPoints, $totalCreditHours),
                ];
            })
            ->sort(function (array $a, array $b) use ($semesterOrder): int {
                preg_match('/^(\d{4})/', (string) $a['academic_year'], $matchA);
                preg_match('/^(\d{4})/', (string) $b['academic_year'], $matchB);

                $startYearA = isset($matchA[1]) ? (int) $matchA[1] : 0;
                $startYearB = isset($matchB[1]) ? (int) $matchB[1] : 0;

                if ($startYearA === $startYearB) {
                    return ($semesterOrder[$b['semester']] ?? 99) <=> ($semesterOrder[$a['semester']] ?? 99);
                }

                return $startYearB <=> $startYearA;
            })
            ->values();
    }

    public function refreshPerformance(): void
    {
        $subjectSummaries = $this->subjectPerformanceSummaries();

        $totalCreditHours = (int) $subjectSummaries->sum(fn (array $item): int => (int) $item['credit_hours']);
        $totalQualityPoints = (float) $subjectSummaries->sum(fn (array $item): float => (float) $item['quality_points']);

        $termSummaries = $this->termPerformanceSummaries();
        $currentGpa = (float) ($termSummaries->first()['gpa'] ?? 0.0);
        $currentCgpa = AcademicCalculator::gpa($totalQualityPoints, $totalCreditHours);

        $this->update([
            'current_gpa' => $currentGpa,
            'current_cgpa' => $currentCgpa,
            'total_completed_credit_hours' => $totalCreditHours,
            'total_quality_points' => $totalQualityPoints,
        ]);
    }
}
