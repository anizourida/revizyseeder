<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('week_id')->nullable()->constrained('weeks')->nullOnDelete();
            $table->string('filename');
            $table->text('local_path')->nullable();
            $table->text('original_url')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->boolean('is_downloaded')->default(false);
            $table->boolean('is_integrity_checked')->default(false);
            $table->boolean('is_corrupt')->default(false);
            $table->boolean('is_vocab_extracted')->default(false);
            $table->string('session_id')->nullable();
            $table->unsignedInteger('vocab_count')->default(0);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            $table->unique(['week_id', 'filename']);
            $table->index([
                'is_downloaded',
                'is_integrity_checked',
                'is_vocab_extracted',
                'session_id',
            ], 'file_assets_sync_state_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_assets');
    }
};
