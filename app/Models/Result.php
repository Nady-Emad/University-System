<?php

namespace App\Models;

use App\Support\AcademicCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'exam_id',
        'marks',
        'grade_point',
        'credit_hours',
        'quality_points',
        'semester',
        'academic_year',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'credit_hours' => 'integer',
            'quality_points' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public static function gradePointFromMarks(float $marks): float
    {
        return AcademicCalculator::gradePointFromMarks($marks);
    }
}
