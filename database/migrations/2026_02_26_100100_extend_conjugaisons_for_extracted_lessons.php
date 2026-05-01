<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conjugaisons', function (Blueprint $table): void {
            $table->foreignId('grade_id')
                ->nullable()
                ->after('id')
                ->constrained('conjugaison_grades')
                ->nullOnDelete();
            $table->foreignId('period_id')
                ->nullable()
                ->after('grade_id')
                ->constrained('conjugaison_periods')
                ->nullOnDelete();
            $table->foreignId('week_id')
                ->nullable()
                ->after('period_id')
                ->constrained('conjugaison_weeks')
                ->nullOnDelete();

            $table->string('name')->nullable()->after('sem');
            $table->longText('related_raw_data')->nullable()->after('raw_data');
            $table->string('source_lesson_id')->nullable()->after('related_raw_data');
            $table->unsignedInteger('source_slide_id')->nullable()->after('source_lesson_id');
            $table->foreignId('source_file_asset_id')
                ->nullable()
                ->after('source_slide_id')
                ->constrained('file_assets')
                ->nullOnDelete();
            $table->unsignedInteger('confidence_score')->default(0)->after('source_file_asset_id');
            $table->json('extraction_meta')->nullable()->after('confidence_score');

            $table->index(['grade_id', 'period_id', 'week_id'], 'conjugaisons_ref_scope_idx');
            $table->index(['source_file_asset_id', 'source_slide_id'], 'conjugaisons_source_slide_idx');
            $table->index(['n', 'p', 'sem', 'confidence_score'], 'conjugaisons_nps_conf_idx');
        });
    }

    public function down(): void
    {
        Schema::table('conjugaisons', function (Blueprint $table): void {
            $table->dropIndex('conjugaisons_ref_scope_idx');
            $table->dropIndex('conjugaisons_source_slide_idx');
            $table->dropIndex('conjugaisons_nps_conf_idx');

            $table->dropConstrainedForeignId('source_file_asset_id');
            $table->dropConstrainedForeignId('week_id');
            $table->dropConstrainedForeignId('period_id');
            $table->dropConstrainedForeignId('grade_id');

            $table->dropColumn([
                'name',
                'related_raw_data',
                'source_lesson_id',
                'source_slide_id',
                'confidence_score',
                'extraction_meta',
            ]);
        });
    }
};
