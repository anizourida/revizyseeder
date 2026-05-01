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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('ocr_olmocr_path')->nullable()->after('ocr_full_text_path');
            $table->string('ocr_chandra_path')->nullable()->after('ocr_olmocr_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['ocr_olmocr_path', 'ocr_chandra_path']);
        });
    }
};
