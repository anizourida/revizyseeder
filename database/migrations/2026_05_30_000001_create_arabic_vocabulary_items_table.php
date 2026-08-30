<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arabic_vocabulary_items', function (Blueprint $table) {
            $table->id();
            $table->string('word', 255);
            $table->string('raw_word', 255)->nullable()->index();
            $table->string('root', 100)->nullable();
            $table->text('example_sentence')->nullable();
            $table->string('strategy', 100)->nullable();
            $table->string('grade', 10)->index();
            $table->string('subject', 20)->default('AR');
            $table->string('period', 10)->index();
            $table->string('week', 10)->index();
            $table->string('lesson_id', 100)->index();
            $table->unsignedInteger('slide_index')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('audio_path', 500)->nullable();
            $table->string('revizy_image_file_id', 100)->nullable();
            $table->string('revizy_audio_file_id', 100)->nullable();
            $table->unsignedBigInteger('revizy_skill_id')->nullable();
            $table->unsignedBigInteger('revizy_unite_id')->nullable();
            $table->timestamp('extracted_at')->nullable();
            $table->timestamps();

            $table->unique(['word', 'lesson_id', 'grade'], 'ar_vocab_unique_word_lesson_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arabic_vocabulary_items');
    }
};
