<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Model;

class RevizyCurriculumMapping extends Model
{
    protected $guarded = [];

    protected $casts = [
        'grade_index' => 'int',
        'period_index' => 'int',
        'revizy_grade_id' => 'int',
        'revizy_subject_id' => 'int',
        'revizy_unite_id' => 'int',
        'revizy_vocab_skill_id' => 'int',
        'revizy_conjugaison_skill_id' => 'int',
        'meta' => 'array',
    ];
}

