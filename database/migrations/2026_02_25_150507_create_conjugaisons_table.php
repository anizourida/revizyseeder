<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conjugaisons', function (Blueprint $table) {
            $table->id();
            $table->string('n');
            $table->string('p');
            $table->string('sem');
            $table->string('verbe')->nullable();
            $table->string('tense')->nullable();
            $table->longText('raw_data');
            $table->string('concept_id')->nullable();
            $table->unsignedInteger('week')->nullable();
            $table->unsignedBigInteger('revizy_skill_id')->nullable();
            $table->unsignedBigInteger('revizy_unite_id')->nullable();
            $table->timestamp('extracted_at')->nullable();
            $table->timestamps();

            $table->index('n');
            $table->index('p');
            $table->index('sem');
            $table->index('concept_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conjugaisons');
    }
};
