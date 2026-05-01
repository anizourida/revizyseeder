<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revizy_curriculum_mappings', function (Blueprint $table): void {
            $table->id();

            // Seeder-facing scope keys (aligned with VocabularyItem: subject=N?, period=P?)
            $table->string('subject_code', 20)->default('FR');
            $table->string('grade_code', 10);
            $table->unsignedTinyInteger('grade_index')->nullable();
            $table->string('period_code', 10);
            $table->unsignedTinyInteger('period_index')->nullable();

            // Revizy references (from production exports)
            $table->unsignedBigInteger('revizy_grade_id')->nullable();
            $table->string('revizy_grade_name')->nullable();
            $table->unsignedBigInteger('revizy_subject_id')->nullable();
            $table->string('revizy_subject_name')->nullable();

            $table->unsignedBigInteger('revizy_unite_id')->nullable();
            $table->string('revizy_unite_name')->nullable();
            $table->string('revizy_unite_code')->nullable();
            $table->string('revizy_unite_index', 20)->nullable();

            // Skills most used by Seeder workflows
            $table->unsignedBigInteger('revizy_vocab_skill_id')->nullable();
            $table->string('revizy_vocab_skill_name')->nullable();
            $table->unsignedBigInteger('revizy_conjugaison_skill_id')->nullable();
            $table->string('revizy_conjugaison_skill_name')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(
                ['subject_code', 'grade_code', 'period_code'],
                'revizy_curriculum_scope_unique'
            );

            $table->index(['subject_code', 'grade_code']);
            $table->index(['subject_code', 'period_code']);
            $table->index(['revizy_unite_id']);
            $table->index(['revizy_vocab_skill_id']);
            $table->index(['revizy_conjugaison_skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revizy_curriculum_mappings');
    }
};

