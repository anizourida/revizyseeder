<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_sentences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vocabulary_item_id')->nullable()->index();
            $table->string('word')->index();
            $table->string('base_word')->nullable()->index();
            $table->string('grade', 20)->index();
            $table->string('subject', 20)->default('FR')->index();
            $table->string('period', 20)->index();
            $table->string('week', 20)->index();
            $table->string('lesson_id', 100)->index();
            $table->text('sentence')->nullable();
            $table->text('sentence_ar')->nullable();
            $table->string('source_session', 20)->nullable();
            $table->integer('source_slide')->nullable();
            $table->string('source_type', 50)->default('slide');
            $table->text('image_path')->nullable();
            $table->text('audio_path')->nullable();
            $table->string('revizy_audio_file_id', 100)->nullable();
            $table->timestamps();

            $table->foreign('vocabulary_item_id')
                ->references('id')
                ->on('vocabulary_items')
                ->nullOnDelete();

            $table->index(['grade', 'period', 'week'], 'vocab_sent_grade_period_week_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_sentences');
    }
};
