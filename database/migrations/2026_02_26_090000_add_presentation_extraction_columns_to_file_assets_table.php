<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_assets', function (Blueprint $table): void {
            $table->boolean('is_presentation_data_extracted')
                ->default(false)
                ->after('is_vocab_extracted');
            $table->unsignedInteger('presentation_slide_count')
                ->default(0)
                ->after('vocab_count');
            $table->text('presentation_json_path')
                ->nullable()
                ->after('presentation_slide_count');
            $table->text('presentation_assets_dir')
                ->nullable()
                ->after('presentation_json_path');
            $table->text('presentation_extraction_error')
                ->nullable()
                ->after('presentation_assets_dir');
            $table->timestamp('presentation_extracted_at')
                ->nullable()
                ->after('presentation_extraction_error');

            $table->index(
                ['is_downloaded', 'is_presentation_data_extracted'],
                'file_assets_presentation_extract_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('file_assets', function (Blueprint $table): void {
            $table->dropIndex('file_assets_presentation_extract_idx');
            $table->dropColumn([
                'is_presentation_data_extracted',
                'presentation_slide_count',
                'presentation_json_path',
                'presentation_assets_dir',
                'presentation_extraction_error',
                'presentation_extracted_at',
            ]);
        });
    }
};
