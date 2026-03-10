<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamQuestion extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'exam_id',
        'question_text',
        'mark',
        'order_no',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mark' => 'decimal:2',
            'order_no' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class, 'exam_id');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(QuestionChoice::class, 'question_id');
    }
}
