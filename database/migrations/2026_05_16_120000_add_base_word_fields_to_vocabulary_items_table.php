<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocabulary_items', function (Blueprint $table) {
            $table->string('base_word')->nullable()->after('word');
            $table->text('base_word_audio_path')->nullable()->after('audio_path');

            $table->index('base_word');
        });
    }

    public function down(): void
    {
        Schema::table('vocabulary_items', function (Blueprint $table) {
            $table->dropIndex(['base_word']);
            $table->dropColumn(['base_word', 'base_word_audio_path']);
        });
    }
};

