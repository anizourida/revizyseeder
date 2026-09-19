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
        Schema::table('arabic_vocabulary_items', function (Blueprint $table) {
            $table->string('clean_word', 255)->nullable()->after('raw_word')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabic_vocabulary_items', function (Blueprint $table) {
            $table->dropColumn('clean_word');
        });
    }
};
