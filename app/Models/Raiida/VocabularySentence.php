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

    public function fileAsset(): BelongsTo
    {
        return $this->belongsTo(FileAsset::class, 'file_asset_id');
    }

    /**
     * Resolve the corresponding FileAsset for presentation preview.
     */
    public function resolveFileAsset(): ?FileAsset
    {
        if ($this->file_asset_id) {
            return $this->fileAsset;
        }

        $asset = null;
        if ($this->source_session) {
            $prefix = 'FR_' . $this->grade . '_' . $this->period . '_' . $this->week . '_' . $this->source_session;
            $asset = FileAsset::where('filename', 'like', $prefix . '%')
                ->orWhere('presentation_json_path', 'like', '%' . $prefix . '%')
                ->first();
        }

        if (! $asset && $this->lesson_id) {
            $asset = FileAsset::where('filename', 'like', $this->lesson_id . '%')
                ->orWhere('presentation_json_path', 'like', '%' . $this->lesson_id . '%')
                ->first();
        }

        return $asset;
    }

    /**
     * Get the direct preview URL to the PPT presentation slide with highlighted text emplacement.
     */
    public function getPreviewUrlAttribute(): ?string
    {
        $asset = $this->file_asset_id ? $this->fileAsset : $this->resolveFileAsset();
        if (! $asset) {
            return null;
        }

        return route('admin.files.preview', [
            'fileAsset' => $asset->id,
            'slide' => $this->source_slide ?: 1,
            'highlight' => $this->sentence ?: $this->word,
        ]);
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
