<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocabulary_sentences', function (Blueprint $table) {
            $table->unsignedBigInteger('file_asset_id')->nullable()->after('vocabulary_item_id');

            $table->foreign('file_asset_id')
                ->references('id')
                ->on('file_assets')
                ->nullOnDelete();

            $table->index('file_asset_id');
        });
    }

    public function down(): void
    {
        Schema::table('vocabulary_sentences', function (Blueprint $table) {
            $table->dropForeign(['file_asset_id']);
            $table->dropColumn('file_asset_id');
        });
    }
};
