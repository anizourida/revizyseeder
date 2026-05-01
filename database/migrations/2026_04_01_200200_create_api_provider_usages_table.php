<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_provider_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->string('period_key', 7);
            $table->unsignedBigInteger('requests_count')->default(0);
            $table->unsignedBigInteger('input_tokens_count')->default(0);
            $table->unsignedBigInteger('output_tokens_count')->default(0);
            $table->unsignedBigInteger('total_tokens_count')->default(0);
            $table->unsignedBigInteger('characters_count')->default(0);
            $table->unsignedBigInteger('remote_used')->nullable();
            $table->unsignedBigInteger('remote_limit')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['api_provider_id', 'period_key'], 'api_provider_usages_unique_period');
            $table->index('period_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_provider_usages');
    }
};

