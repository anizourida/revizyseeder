<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('student_code');
            $table->string('student_name')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'student_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_students');
    }
};
