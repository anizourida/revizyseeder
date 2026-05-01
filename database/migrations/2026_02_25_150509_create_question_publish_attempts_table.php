<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_publish_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_question_id');
            $table->string('concept_id');
            $table->string('name');
            $table->longText('question_data');
            $table->string('status')->default('pending');
            $table->string('revizy_question_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('unaccepted_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index('local_question_id');
            $table->index('concept_id');
            $table->index(['concept_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_publish_attempts');
    }
};
