<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('conjugaisons')
            ->select('n', 'p', 'sem', DB::raw('COUNT(*) as total'))
            ->groupBy('n', 'p', 'sem')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('conjugaisons')
                ->where('n', (string) $duplicate->n)
                ->where('p', (string) $duplicate->p)
                ->where('sem', (string) $duplicate->sem)
                ->orderByRaw("CASE WHEN source_lesson_id IS NULL OR source_lesson_id = '' THEN 0 ELSE 1 END DESC")
                ->orderByDesc('confidence_score')
                ->orderByDesc('id')
                ->pluck('id')
                ->values();

            $keepId = $ids->first();
            if ($keepId === null) {
                continue;
            }

            $deleteIds = $ids->filter(static fn (int $id): bool => $id !== (int) $keepId)->all();
            if ($deleteIds !== []) {
                DB::table('conjugaisons')->whereIn('id', $deleteIds)->delete();
            }
        }

        Schema::table('conjugaisons', function (Blueprint $table): void {
            $table->unique(['n', 'p', 'sem'], 'conjugaisons_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::table('conjugaisons', function (Blueprint $table): void {
            $table->dropUnique('conjugaisons_scope_unique');
        });
    }
};

