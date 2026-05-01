<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('provider_type')->default('custom');
            $table->string('display_name')->nullable();
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable();
            $table->string('model')->nullable();
            $table->string('usage_endpoint')->nullable();
            $table->unsignedBigInteger('monthly_limit')->nullable();
            $table->string('limit_unit')->default('requests');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['provider_type', 'is_active'], 'api_providers_type_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_providers');
    }
};

