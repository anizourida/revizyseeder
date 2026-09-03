<?php

namespace App\Console\Commands;

use App\Services\Raiida\VocabularySentenceExtractionService;
use Illuminate\Console\Command;
use Throwable;

class ExtractVocabularySentencesCommand extends Command
{
    protected $signature = 'revizyseeder:extract-vocabulary-sentences
        {--grade= : Filter by grade (e.g. N1, N2, N3, N4, N5, N6)}
        {--period= : Filter by period (e.g. P1, P2, P3, P4, P5)}
        {--week= : Filter by week (e.g. SEM1, SEM2, SEM3, SEM4)}
        {--lesson= : Filter by lesson ID (e.g. FR_N2_P1_SEM1_S1)}
        {--force : Overwrite existing sentence records}';

    protected $description = 'Extract French vocabulary sentences from presentation slides and OCR data.';

    public function handle(VocabularySentenceExtractionService $service): int
    {
        $options = [
            'grade' => (string) $this->option('grade'),
            'period' => (string) $this->option('period'),
            'week' => (string) $this->option('week'),
            'lesson_id' => (string) $this->option('lesson'),
            'force' => (bool) $this->option('force'),
        ];

        $this->info('Starting French vocabulary sentence extraction...');
        if ($options['grade']) $this->line("Grade: {$options['grade']}");
        if ($options['period']) $this->line("Period: {$options['period']}");
        if ($options['week']) $this->line("Week: {$options['week']}");
        if ($options['lesson_id']) $this->line("Lesson: {$options['lesson_id']}");
        if ($options['force']) $this->warn("Force mode: existing records will be refreshed.");

        try {
            $stats = $service->extractSentences($options);

            $this->newLine();
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Vocabulary Items Processed', $stats['total_vocabs']],
                    ['Vocabulary Items With Sentences', $stats['vocabs_with_sentences']],
                    ['Vocabulary Items Without Sentences', $stats['vocabs_without_sentences']],
                    ['Total Sentences Created', $stats['sentences_created']],
                ]
            );

            $this->info('Vocabulary sentence extraction completed successfully!');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Extraction failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
