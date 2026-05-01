<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conjugaison extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'grade_id' => 'int',
        'period_id' => 'int',
        'week_id' => 'int',
        'week' => 'int',
        'source_slide_id' => 'int',
        'source_file_asset_id' => 'int',
        'confidence_score' => 'int',
        'extraction_meta' => 'array',
        'revizy_skill_id' => 'int',
        'revizy_unite_id' => 'int',
        'extracted_at' => 'datetime',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(ConjugaisonGrade::class, 'grade_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(ConjugaisonPeriod::class, 'period_id');
    }

    public function semWeek(): BelongsTo
    {
        return $this->belongsTo(ConjugaisonWeek::class, 'week_id');
    }

    public function sourceFileAsset(): BelongsTo
    {
        return $this->belongsTo(FileAsset::class, 'source_file_asset_id');
    }
}
