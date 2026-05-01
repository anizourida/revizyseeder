<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conjugaison_grades', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('grade_number')->unique();
            $table->string('code', 8)->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('conjugaison_periods', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('period_number')->unique();
            $table->string('code', 8)->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('conjugaison_weeks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('week_number')->unique();
            $table->string('code', 16)->unique();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conjugaison_weeks');
        Schema::dropIfExists('conjugaison_periods');
        Schema::dropIfExists('conjugaison_grades');
    }
};
