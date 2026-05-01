<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherStudent extends Model
{
    protected $fillable = [
        'teacher_id',
        'student_code',
        'student_name',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
