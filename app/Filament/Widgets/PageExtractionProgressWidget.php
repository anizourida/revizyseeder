<?php

namespace App\Filament\Widgets;

use App\Models\Raiida\Grade;
use App\Models\Raiida\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use App\Support\RevizySeeder\WorkflowState;

class PageExtractionProgressWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPages = Page::count();
        $extractedPages = Page::whereNotNull('page_number')
            ->where('page_number', '>', 0)
            ->count();
        $failedPages = Page::where('page_number_extraction_method', 'ocr_failed')
            ->orWhereNotNull('page_number_extraction_error')
            ->count();
        $pendingPages = $totalPages - $extractedPages;

        $percentage = $totalPages > 0 ? round(($extractedPages / $totalPages) * 100, 1) : 0;

        $pendingJobsCount = DB::table('jobs')
            ->where('queue', 'revizyseeder-workflows')
            ->count();

        $failedJobsCount = DB::table('failed_jobs')->count();
        $paused = WorkflowState::isPaused();

        // Overall stats
        $stats = [];

        $overallColor = 'warning';
        if ($percentage >= 100) $overallColor = 'success';
        if ($percentage < 30) $overallColor = 'danger';

        $stats[] = Stat::make('Page Extraction Progress', "{$extractedPages} / {$totalPages}")
            ->description("{$percentage}% complete · {$pendingPages} remaining")
            ->color($overallColor)
            ->chart([$totalPages - $extractedPages, $extractedPages]);

        $queueDescription = $paused
            ? 'Paused from dashboard'
            : ($failedJobsCount > 0 ? "⚠ {$failedJobsCount} failed jobs" : '✓ No failed jobs');

        $stats[] = Stat::make('Queue Status', "{$pendingJobsCount} pending")
            ->description($queueDescription)
            ->color($paused ? 'gray' : ($failedJobsCount > 0 ? 'danger' : ($pendingJobsCount > 0 ? 'warning' : 'success')));

        $stats[] = Stat::make('Extraction Errors', (string) $failedPages)
            ->description($failedPages > 0 ? 'Pages with extraction errors' : 'No errors')
            ->color($failedPages > 0 ? 'danger' : 'success');

        // Per-grade breakdown
        $grades = Grade::withCount([
            'pages',
            'pages as pages_extracted_count' => function ($query) {
                $query->whereNotNull('page_number')->where('page_number', '>', 0);
            },
        ])->get();

        foreach ($grades as $grade) {
            $total = $grade->pages_count;
            $extracted = $grade->pages_extracted_count;
            $remaining = $total - $extracted;
            $pct = $total > 0 ? round(($extracted / $total) * 100, 1) : 0;

            $color = 'warning';
            if ($pct >= 100) $color = 'success';
            if ($pct < 30) $color = 'danger';

            $stats[] = Stat::make("Grade {$grade->name}", "{$extracted} / {$total}")
                ->description("{$pct}% · {$remaining} remaining")
                ->color($color);
        }

        return $stats;
    }
}
