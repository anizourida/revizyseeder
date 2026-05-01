<?php

namespace Database\Seeders;

use App\Models\Raiida\ConjugaisonGrade;
use App\Models\Raiida\ConjugaisonPeriod;
use App\Models\Raiida\ConjugaisonWeek;
use Illuminate\Database\Seeder;

class ConjugaisonReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 6) as $gradeNumber) {
            ConjugaisonGrade::query()->updateOrCreate(
                ['grade_number' => $gradeNumber],
                [
                    'code' => 'N' . $gradeNumber,
                    'label' => 'Grade ' . $gradeNumber,
                ]
            );
        }

        foreach (range(1, 5) as $periodNumber) {
            ConjugaisonPeriod::query()->updateOrCreate(
                ['period_number' => $periodNumber],
                [
                    'code' => 'P' . $periodNumber,
                    'label' => 'Period ' . $periodNumber,
                ]
            );
        }

        foreach (range(1, 6) as $weekNumber) {
            ConjugaisonWeek::query()->updateOrCreate(
                ['week_number' => $weekNumber],
                [
                    'code' => 'SEM' . $weekNumber,
                    'label' => 'Week ' . $weekNumber,
                ]
            );
        }
    }
}
