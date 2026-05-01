<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_items', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->text('image_path')->nullable();
            $table->text('audio_path')->nullable();

            $table->string('grade');
            $table->string('subject')->default('FR');
            $table->string('period');
            $table->string('week');
            $table->string('lesson_id');
            $table->string('ar_translation')->nullable();

            $table->string('lexical_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('distractor_group')->nullable();
            $table->string('distractor_subgroup')->nullable();

            $table->string('revizy_image_file_id')->nullable();
            $table->string('revizy_audio_file_id')->nullable();
            $table->string('walidio_image_id')->nullable();
            $table->string('flashcard_id')->nullable();
            $table->string('concept_id')->nullable();
            $table->unsignedBigInteger('revizy_skill_id')->nullable();
            $table->unsignedBigInteger('revizy_unite_id')->nullable();

            $table->timestamp('extracted_at')->nullable();
            $table->timestamps();

            $table->unique(['word', 'lesson_id', 'grade']);
            $table->index(['grade', 'period', 'week'], 'vocab_grade_period_week_idx');
            $table->index('word');
            $table->index('grade');
            $table->index('subject');
            $table->index('period');
            $table->index('week');
            $table->index('lesson_id');
            $table->index('concept_id');
            $table->index('revizy_image_file_id');
            $table->index('revizy_audio_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_items');
    }
};
