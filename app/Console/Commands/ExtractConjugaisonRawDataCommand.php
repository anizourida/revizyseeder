<?php

namespace App\Console\Commands;

use App\Services\Raiida\ConjugaisonRawDataExtractor;
use Illuminate\Console\Command;

class ExtractConjugaisonRawDataCommand extends Command
{
    protected $signature = 'revizyseeder:extract-conjugaison-raw-data
        {--n=  : Grade code, e.g. N4}
        {--p=  : Period code, e.g. P1}
        {--sem= : Week code, e.g. SEM1}
        {--all : Run extraction for ALL grades (N1-N6), periods (P1-P5), and weeks (SEM1-SEM6)}
        {--dry-run : Preview extracted data without saving}';

    protected $description = 'Extract ALL conjugaison-related raw data from lesson presentations for a given scope (N/P/SEM)';

    public function handle(ConjugaisonRawDataExtractor $extractor): int
    {
        $this->call('app:db-backup');
        $isDryRun = (bool) $this->option('dry-run');
        $isAll = (bool) $this->option('all');

        if ($isAll) {
            return $this->handleAll($extractor, $isDryRun);
        }

        $n = strtoupper(trim((string) $this->option('n')));
        $p = strtoupper(trim((string) $this->option('p')));
        $sem = strtoupper(trim((string) $this->option('sem')));

        if ($n === '' || $p === '' || $sem === '') {
            $this->error('Provide --n, --p, and --sem, OR use --all to run for everything.');
            $this->line('Example: php artisan revizyseeder:extract-conjugaison-raw-data --n=N4 --p=P1 --sem=SEM1');

            return self::FAILURE;
        }

        if (! preg_match('/^N[1-6]$/', $n)) {
            $this->error("Invalid grade code '{$n}'. Must be N1-N6.");

            return self::FAILURE;
        }

        if (! preg_match('/^P[1-5]$/', $p)) {
            $this->error("Invalid period code '{$p}'. Must be P1-P5.");

            return self::FAILURE;
        }

        if (! preg_match('/^SEM[1-6]$/', $sem)) {
            $this->error("Invalid week code '{$sem}'. Must be SEM1-SEM6.");

            return self::FAILURE;
        }

        return $this->extractSingle($extractor, $n, $p, $sem, $isDryRun, true);
    }

    private function handleAll(ConjugaisonRawDataExtractor $extractor, bool $isDryRun): int
    {
        $this->info('🚀  Running conjugaison raw data extraction for ALL scopes' . ($isDryRun ? ' [DRY RUN]' : ''));
        $this->newLine();

        $totalScopes = 0;
        $scopesWithData = 0;
        $scopesEmpty = 0;
        $totalItems = 0;

        $grades = range(1, 6);
        $periods = range(1, 5);
        $weeks = range(1, 6);

        $totalExpected = count($grades) * count($periods) * count($weeks);
        $bar = $this->output->createProgressBar($totalExpected);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        $results = [];

        foreach ($grades as $gradeNum) {
            foreach ($periods as $periodNum) {
                foreach ($weeks as $weekNum) {
                    $n = 'N' . $gradeNum;
                    $p = 'P' . $periodNum;
                    $sem = 'SEM' . $weekNum;

                    $totalScopes++;
                    $bar->setMessage("{$n}/{$p}/{$sem}");

                    if ($isDryRun) {
                        $result = $extractor->extract($n, $p, $sem);
                    } else {
                        $result = $extractor->extractAndPersist($n, $p, $sem);
                    }

                    $itemCount = count($result['items']);

                    if ($itemCount > 0) {
                        $scopesWithData++;
                        $totalItems += $itemCount;

                        // Deduplicate for unique count
                        $seen = [];
                        foreach ($result['items'] as $item) {
                            $seen[mb_strtolower(trim($item['text']))] = true;
                        }

                        $results[] = [
                            'scope' => "{$n}/{$p}/{$sem}",
                            'sessions' => $result['summary']['sessions_found'],
                            'matched' => $itemCount,
                            'unique' => count($seen),
                        ];
                    } else {
                        $scopesEmpty++;
                    }

                    $bar->advance();
                }
            }
        }

        $bar->setMessage('Done!');
        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("📊  Batch Extraction Summary:");
        $this->line("  Total scopes processed: {$totalScopes}");
        $this->line("  Scopes with data:       {$scopesWithData}");
        $this->line("  Scopes empty:           {$scopesEmpty}");
        $this->line("  Total items extracted:   {$totalItems}");
        $this->newLine();

        if ($results !== []) {
            $this->info("📝  Scopes with conjugaison data:");
            $this->table(
                ['Scope', 'Sessions', 'Matched', 'Unique Lines'],
                $results
            );
        }

        if ($isDryRun) {
            $this->newLine();
            $this->info('ℹ️  Dry run complete. Use without --dry-run to persist.');
        } else {
            $this->newLine();
            $this->info("✅  All data persisted to conjugaisons table.");
        }

        return self::SUCCESS;
    }

    private function extractSingle(
        ConjugaisonRawDataExtractor $extractor,
        string $n,
        string $p,
        string $sem,
        bool $isDryRun,
        bool $verbose
    ): int {
        $this->info("Extracting conjugaison raw data for {$n}/{$p}/{$sem}" . ($isDryRun ? ' [DRY RUN]' : ''));
        $this->newLine();

        if ($isDryRun) {
            $result = $extractor->extract($n, $p, $sem);
        } else {
            $result = $extractor->extractAndPersist($n, $p, $sem);
        }

        $summary = $result['summary'];
        $items = $result['items'];

        $this->info("📊  Extraction Summary:");
        $this->line("  Sessions found:    {$summary['sessions_found']}");
        $this->line("  Slides scanned:    {$summary['slides_scanned']}");
        $this->line("  Texts scanned:     {$summary['texts_scanned']}");
        $this->line("  Texts matched:     {$summary['texts_matched']}");
        $this->newLine();

        if ($items === []) {
            $this->warn('No conjugaison-related content found for this scope.');

            return self::SUCCESS;
        }

        if ($verbose) {
            // Group by type for display
            $byType = [];
            foreach ($items as $item) {
                $byType[$item['type']][] = $item;
            }

            $this->info("📝  Extracted Items by Type:");
            foreach ($byType as $type => $typeItems) {
                $this->newLine();
                $label = str_replace('_', ' ', ucfirst($type));
                $this->line("  <comment>{$label}</comment> (" . count($typeItems) . ')');
                foreach ($typeItems as $item) {
                    $session = $item['session'];
                    $slideId = $item['slide_id'];
                    $text = mb_strlen($item['text']) > 100
                        ? mb_substr($item['text'], 0, 100) . '…'
                        : $item['text'];
                    $this->line("    [{$session} / Slide {$slideId}] {$text}");
                }
            }

            $this->newLine();

            // Show deduplicated raw output
            $seen = [];
            $uniqueLines = [];
            foreach ($items as $item) {
                $key = mb_strtolower(trim($item['text']));
                if (! isset($seen[$key])) {
                    $uniqueLines[] = $item['text'];
                    $seen[$key] = true;
                }
            }

            $this->info("📋  Raw Data Output (" . count($uniqueLines) . ' unique lines):');
            $this->newLine();
            foreach ($uniqueLines as $line) {
                $this->line("  " . $line);
            }
            $this->newLine();
        }

        if (! $isDryRun) {
            $persisted = $result['persisted'] ?? false;
            if ($persisted) {
                $this->info("✅  Data persisted to conjugaisons table for {$n}/{$p}/{$sem}");
            } else {
                $this->warn("⚠️  Data was NOT persisted (no matching row found).");
            }
        } else {
            $this->info('ℹ️  Dry run complete. Use without --dry-run to persist.');
        }

        return self::SUCCESS;
    }
}
