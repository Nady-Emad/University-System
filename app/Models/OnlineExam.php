<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnlineExam extends Model
{
    use HasFactory;

    protected $table = 'exams_new';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'exam_type',
        'subject_id',
        'doctor_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks',
        'status',
        'allow_retake',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'duration_minutes' => 'integer',
            'total_marks' => 'decimal:2',
            'allow_retake' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id')->orderBy('order_no');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(StudentExamAttempt::class, 'exam_id');
    }

    public function isPublishedNow(): bool
    {
        $now = now();

        return $this->status === 'published' && $now->between($this->start_time, $this->end_time);
    }

    public function isOpenForStudent(): bool
    {
        $now = now();

        return $this->status === 'published' && $now->greaterThanOrEqualTo($this->start_time) && $now->lessThanOrEqualTo($this->end_time);
    }
}
