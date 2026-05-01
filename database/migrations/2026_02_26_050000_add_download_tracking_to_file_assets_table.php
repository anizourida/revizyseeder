<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_assets', function (Blueprint $table) {
            $table->string('download_state', 20)->default('pending')->after('is_downloaded');
            $table->unsignedTinyInteger('download_progress')->default(0)->after('download_state');
            $table->text('download_error')->nullable()->after('download_progress');
            $table->timestamp('download_started_at')->nullable()->after('download_error');
            $table->timestamp('download_finished_at')->nullable()->after('download_started_at');
            $table->index('download_state', 'file_assets_download_state_idx');
        });

        DB::table('file_assets')
            ->where('is_downloaded', true)
            ->update([
                'download_state' => 'downloaded',
                'download_progress' => 100,
            ]);
    }

    public function down(): void
    {
        Schema::table('file_assets', function (Blueprint $table) {
            $table->dropIndex('file_assets_download_state_idx');
            $table->dropColumn([
                'download_state',
                'download_progress',
                'download_error',
                'download_started_at',
                'download_finished_at',
            ]);
        });
    }
};
