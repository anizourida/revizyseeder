<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPublishAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'local_question_id' => 'int',
        'published_at' => 'datetime',
        'unaccepted_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}
