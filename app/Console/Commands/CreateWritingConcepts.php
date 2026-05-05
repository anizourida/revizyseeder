<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateWritingConcepts extends Command
{
    protected $signature = 'app:create-writing-concepts {--dry-run : Only show what would be created} {--cleanup : Delete old concepts before creating new ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Revizy concepts for Grade 1 writing activities (SEM1-SEM4) with separate Majuscule/Minuscule/Accented versions';

    public function __construct(
        private readonly \App\Services\Raiida\External\RevizySystemClient $revizy,
        private readonly \App\Services\Raiida\RevizyCurriculumMappingSyncService $mappingSync
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Writing Concept Generation for Grade 1...');

        if ($this->option('cleanup')) {
            $this->cleanupOldConcepts();
        }

        $pages = \App\Models\Raiida\Page::where('grade_id', 1)
            ->where('activity_category', 'Activités d’écriture')
            ->where('n_p_sem', 'not like', '%SEM5%')
            ->orderBy('n_p_sem')
            ->get();

        if ($pages->isEmpty()) {
            $this->warn('No writing activity pages found for Grade 1 (SEM1-SEM4).');
            return;
        }

        $this->info('Syncing mappings for Grade 1 periods...');
        foreach (['P1', 'P2', 'P3', 'P4'] as $pCode) {
            try {
                $this->mappingSync->syncScope('FR', 'N1', $pCode);
            } catch (\Throwable $e) {
                $this->error("Failed to sync mapping for N1/{$pCode}: " . $e->getMessage());
            }
        }

        $newMapping = [];

        foreach ($pages as $page) {
            $scope = $this->resolveScope($page);
            if (!$scope) continue;

            $mapping = \App\Models\Raiida\RevizyCurriculumMapping::where('grade_code', 'N1')
                ->where('period_code', $scope['period_code'])
                ->first();

            if (!$mapping || !$mapping->revizy_unite_id) {
                $this->error("No mapping/unite found for {$page->n_p_sem}");
                continue;
            }

            $letters = $this->extractLetters($page);
            if ($letters === "N/A") {
                $this->warn("Could not extract letters for {$page->n_p_sem}, skipping.");
                continue;
            }

            $variations = $this->getVariations($letters);
            
            $newMapping[$page->n_p_sem] = [];

            foreach ($variations as $char => $type) {
                $conceptName = "Traçage de la lettre {$char} ({$type})";
                
                if ($this->option('dry-run')) {
                    $this->line("[DRY RUN] Would create concept: '{$conceptName}' for {$page->n_p_sem} (Unite: {$mapping->revizy_unite_id}, Week: {$scope['week_number']})");
                    continue;
                }

                try {
                    $payload = [
                        'skill_id' => 15,
                        'unite_id' => $mapping->revizy_unite_id,
                        'name' => $conceptName,
                        'description' => "Activités d’écriture pour {$page->n_p_sem}",
                        'status' => 'published',
                        'is_active' => true,
                        'week' => $scope['week_number'],
                    ];

                    $response = $this->revizy->post('/concepts', $payload);
                    $conceptId = $this->revizy->extractResourceId($response);

                    if ($conceptId) {
                        $this->info("✅ Created concept ID {$conceptId} for {$page->n_p_sem}: {$conceptName}");
                        $newMapping[$page->n_p_sem][] = [
                            'id' => $conceptId,
                            'name' => $conceptName,
                            'char' => $char,
                            'type' => $type
                        ];
                    } else {
                        $this->error("❌ API did not return concept ID for {$page->n_p_sem}");
                    }
                } catch (\Throwable $e) {
                    $this->error("❌ Failed to create concept for {$page->n_p_sem}: " . $e->getMessage());
                }

                // Small delay between API calls
                usleep(200000); 
            }
        }

        if (!$this->option('dry-run')) {
            $this->saveMapping($newMapping);
        }

        $this->info('✅ Finished.');
    }

    private function cleanupOldConcepts(): void
    {
        $mappingPath = storage_path('app/writing_concepts_mapping.json');
        if (!file_exists($mappingPath)) {
            $this->warn('No mapping file found to cleanup.');
            return;
        }

        $mapping = json_decode(file_get_contents($mappingPath), true);
        if (!is_array($mapping)) {
            $this->error('Invalid mapping file format.');
            return;
        }

        $this->info('🧹 Cleaning up old concepts...');
        foreach ($mapping as $key => $value) {
            // Support both old flat format and new structured format
            $ids = is_array($value) ? array_column($value, 'id') : [$value];
            
            foreach ($ids as $id) {
                try {
                    $this->revizy->delete("/concepts/{$id}");
                    $this->line("🗑️ Deleted old concept ID {$id}");
                } catch (\Throwable $e) {
                    $this->warn("⚠️ Failed to delete concept ID {$id}: " . $e->getMessage());
                }
            }
        }
    }

    private function getVariations(string $letters): array
    {
        // Split by common separators
        $chars = preg_split('/[\s\-\–\,\;\/]+/', $letters, -1, PREG_SPLIT_NO_EMPTY);
        $variations = [];

        foreach ($chars as $char) {
            $firstChar = mb_substr($char, 0, 1);
            
            // 1. Identify Variation Type
            if (mb_strtoupper($firstChar) === $firstChar && mb_strtolower($firstChar) !== $firstChar) {
                $type = 'Majuscule';
            } else {
                // Handle accents specifically
                $clean = mb_strtolower($char);
                if ($clean === 'é') {
                    $type = 'Egue';
                } elseif ($clean === 'è') {
                    $type = 'Grave';
                } elseif ($clean === 'ê') {
                    $type = 'Chapeau';
                } else {
                    $type = 'Minuscule';
                }
            }
            
            $variations[$char] = $type;
        }

        return $variations;
    }

    private function saveMapping(array $mapping): void
    {
        $path = storage_path('app/writing_concepts_mapping.json');
        file_put_contents($path, json_encode($mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("💾 Saved new mapping to {$path}");
    }

    private function resolveScope($page): ?array
    {
        if (preg_match('/_N(\d)_P(\d)_SEM(\d)/', $page->n_p_sem, $m)) {
            return [
                'grade_code' => 'N' . $m[1],
                'period_code' => 'P' . $m[2],
                'week_number' => (int) $m[3],
                'week_code' => 'SEM' . $m[3],
            ];
        }
        return null;
    }

    private function extractLetters($page): string
    {
        $ocrPath = $page->ocr_olmocr_path ?: $page->ocr_chandra_path ?: $page->ocr_full_text_path;
        if (!$ocrPath) return "N/A";

        $fullPath = storage_path('app/' . $ocrPath);
        if (!file_exists($fullPath)) return "N/A";

        $content = file_get_contents($fullPath);
        
        // Priority 1: <h1> or <h2> tags (often contains the letter pair like "D - d")
        if (preg_match_all('/<h[12]>(.*?)<\/h[12]>/i', $content, $matches)) {
            foreach ($matches[1] as $match) {
                $clean = trim(strip_tags($match));
                if (!str_contains(mb_strtolower($clean), 'activité') && !str_contains(mb_strtolower($clean), 'semaine')) {
                    return $clean;
                }
            }
        } 
        
        if (preg_match_all('/<h3>(.*?)<\/h3>/i', $content, $matches)) {
            foreach ($matches[1] as $match) {
                $clean = trim(strip_tags($match));
                if (!str_contains(mb_strtolower($clean), 'activité') && !str_contains(mb_strtolower($clean), 'semaine')) {
                    return $clean;
                }
            }
        }

        $text = strip_tags($content);
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        foreach ($lines as $line) {
            $clean = mb_strtolower($line);
            if (!str_contains($clean, 'activité') && !str_contains($clean, 'semaine') && !str_contains($clean, 'période') && strlen($line) < 50) {
                return $line;
            }
        }

        return "N/A";
    }
}
