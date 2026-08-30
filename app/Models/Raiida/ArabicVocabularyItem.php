<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArabicVocabularyItem extends Model
{
    use HasFactory;

    protected $table = 'arabic_vocabulary_items';

    protected $guarded = [];

    protected $casts = [
        'extracted_at' => 'datetime',
        'revizy_skill_id' => 'int',
        'revizy_unite_id' => 'int',
        'slide_index' => 'int',
    ];

    public function scopeGrade(Builder $query, string $grade): Builder
    {
        return $query->where('grade', $grade);
    }

    public function scopePeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    public function scopeWeek(Builder $query, string $week): Builder
    {
        return $query->where('week', $week);
    }

    public function scopeLesson(Builder $query, string $lessonId): Builder
    {
        return $query->where('lesson_id', $lessonId);
    }
}
