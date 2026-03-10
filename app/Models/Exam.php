<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'subject_name',
        'exam_type',
        'exam_date',
        'total_marks',
        'credit_hours',
        'semester',
        'academic_year',
        'doctor_id',
        'source_online_exam_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'total_marks' => 'integer',
            'credit_hours' => 'integer',
            'source_online_exam_id' => 'integer',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function sourceOnlineExam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class, 'source_online_exam_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
