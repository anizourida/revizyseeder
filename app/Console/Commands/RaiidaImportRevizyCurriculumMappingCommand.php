<?php

namespace App\Console\Commands;

use App\Services\Raiida\RevizyCurriculumMappingImportService;
use Illuminate\Console\Command;

class RaiidaImportRevizyCurriculumMappingCommand extends Command
{
    protected $signature = 'raiida:import-revizy-curriculum-mapping
        {--skills= : Path to skills.json exported from Revizy}
        {--unites= : Path to unites.json exported from Revizy}
        {--subject=FR : Subject code used in Seeder (example: FR, AR)}
    ';

    protected $description = 'Import Revizy grade/period mapping (skills + unites) into Seeder DB.';

    public function handle(RevizyCurriculumMappingImportService $importer): int
    {
        $skillsPath = (string) ($this->option('skills') ?? '');
        $unitesPath = (string) ($this->option('unites') ?? '');
        $subjectCode = (string) ($this->option('subject') ?? 'FR');

        if ($skillsPath === '' || $unitesPath === '') {
            $this->error('Missing required options: --skills and --unites');
            $this->line('Example: php artisan raiida:import-revizy-curriculum-mapping --skills=/path/skills.json --unites=/path/unites.json --subject=FR');

            return self::FAILURE;
        }

        $summary = $importer->importFromJsonFiles($skillsPath, $unitesPath, [
            'subject_code' => $subjectCode,
        ]);

        $this->info('Import completed.');
        $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '');

        return self::SUCCESS;
    }
}

