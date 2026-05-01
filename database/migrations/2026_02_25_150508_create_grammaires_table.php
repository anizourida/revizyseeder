<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grammaires', function (Blueprint $table) {
            $table->id();
            $table->string('n');
            $table->string('p');
            $table->string('sem');
            $table->string('objectif')->nullable();
            $table->string('lesson_title')->nullable();
            $table->longText('raw_data');
            $table->timestamp('extracted_at')->nullable();
            $table->timestamps();

            $table->index('n');
            $table->index('p');
            $table->index('sem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grammaires');
    }
};
