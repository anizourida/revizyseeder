<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grammaire extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'extracted_at' => 'datetime',
    ];
}
