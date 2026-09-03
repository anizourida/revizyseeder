<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularySentence extends Model
{
    use HasFactory;

    protected $table = 'vocabulary_sentences';

    protected $guarded = [];

    public function vocabularyItem(): BelongsTo
    {
        return $this->belongsTo(VocabularyItem::class, 'vocabulary_item_id');
    }

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

    public function scopeHasSentence(Builder $query, bool $hasSentence = true): Builder
    {
        if ($hasSentence) {
            return $query->whereNotNull('sentence')->where('sentence', '!=', '');
        }

        return $query->where(function ($q) {
            $q->whereNull('sentence')->orWhere('sentence', '');
        });
    }
}
