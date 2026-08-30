<?php

namespace App\Console\Commands;

use App\Services\Raiida\ArabicVocabularyExtractionService;
use Illuminate\Console\Command;

class ExtractArabicVocabularyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raiida:extract-arabic-vocab
                            {--lesson= : Extract a specific lesson (e.g. AR_N2_P1_SEM1_S1)}
                            {--grade= : Filter by grade (e.g. N1, N2, N3, N4, N5, N6)}
                            {--period= : Filter by period (e.g. P1, P2, P3, P4, P5)}
                            {--week= : Filter by week (e.g. SEM1, SEM2, SEM3, SEM4, SEM5, SEM6)}
                            {--limit= : Limit the number of lessons processed}
                            {--force : Force re-extraction even if already extracted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract Arabic vocabulary items (المعجم) from Arabic lesson presentations';

    /**
     * Execute the console command.
     */
    public function handle(ArabicVocabularyExtractionService $service): int
    {
        $lesson = $this->option('lesson');
        $force = (bool) $this->option('force');

        if ($lesson) {
            $this->info("Extracting Arabic vocabulary for single lesson: {$lesson}");
            $result = $service->extractLesson($lesson, $force);

            if ($result['success']) {
                $count = $result['count'];
                $this->info("Successfully extracted {$count} vocabulary items from {$lesson}.");
                if (! empty($result['items'])) {
                    $rows = [];
                    foreach ($result['items'] as $item) {
                        $rows[] = [
                            'Word' => $item['word'],
                            'Raw Word' => $item['raw_word'],
                            'Strategy' => $item['strategy'] ?? '—',
                            'Image' => $item['image_path'] ?? '—',
                            'Example' => \Illuminate\Support\Str::limit($item['example_sentence'] ?? '—', 40),
                        ];
                    }
                    $this->table(['Word', 'Raw Word', 'Strategy', 'Image', 'Example'], $rows);
                }

                return self::SUCCESS;
            }

            $this->error("Failed: " . ($result['error'] ?? 'Unknown error'));

            return self::FAILURE;
        }

        $options = [
            'grade' => $this->option('grade'),
            'period' => $this->option('period'),
            'week' => $this->option('week'),
            'limit' => $this->option('limit') ? (int) $this->option('limit') : 0,
            'force' => $force,
        ];

        $this->info("Starting batch extraction of Arabic vocabulary...");
        $summary = $service->runBatchExtraction($options);

        $this->info("Batch completed:");
        $this->line("  Total matching files: " . $summary['total']);
        $this->line("  Processed files:      " . $summary['processed']);
        $this->line("  Failed files:         " . $summary['failed']);
        $this->line("  Extracted items:      " . $summary['extracted_total']);

        if (! empty($summary['errors'])) {
            $this->warn("Errors encountered:");
            foreach (array_slice($summary['errors'], 0, 10) as $err) {
                $this->line("  - {$err['file']}: {$err['error']}");
            }
        }

        return self::SUCCESS;
    }
}
