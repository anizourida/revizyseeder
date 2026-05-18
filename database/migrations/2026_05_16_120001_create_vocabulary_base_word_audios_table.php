<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_base_word_audios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vocabulary_item_id')->unique()->constrained('vocabulary_items')->cascadeOnDelete();
            $table->string('revizy_file_id');
            $table->timestamps();

            $table->index('revizy_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_base_word_audios');
    }
};

