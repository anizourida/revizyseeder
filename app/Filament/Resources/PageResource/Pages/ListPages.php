<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Jobs\RevizySeederLMStudioOCRJob;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

use App\Jobs\RevizySeederExtractPagesJob;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Page;
use App\Support\RevizySeeder\WorkflowState;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('extract_pages')
                ->label('Extract New Pages')
                ->icon('heroicon-o-arrow-path')
                ->modalHeading('Extract Pages')
                ->modalDescription('Extract pages from presentation folders matching the selected scope (only N1–N6, no mixed grades).')
                ->form([
                    Forms\Components\Select::make('grade')
                        ->label('Grade')
                        ->options([
                            'N1' => 'N1',
                            'N2' => 'N2',
                            'N3' => 'N3',
                            'N4' => 'N4',
                            'N5' => 'N5',
                            'N6' => 'N6',
                        ])
                        ->placeholder('All grades'),
                    Forms\Components\Select::make('period')
                        ->label('Period')
                        ->options([
                            'P1' => 'P1',
                            'P2' => 'P2',
                            'P3' => 'P3',
                            'P4' => 'P4',
                            'P5' => 'P5',
                        ])
                        ->placeholder('All periods'),
                    Forms\Components\Select::make('week')
                        ->label('Week')
                        ->options([
                            'SEM1' => 'SEM1',
                            'SEM2' => 'SEM2',
                            'SEM3' => 'SEM3',
                            'SEM4' => 'SEM4',
                            'SEM5' => 'SEM5',
                            'SEM6' => 'SEM6',
                        ])
                        ->placeholder('All weeks'),
                ])
                ->action(function (array $data) {
                    $options = [
                        'grade' => $data['grade'] ?? null,
                        'period' => $data['period'] ?? null,
                        'week' => $data['week'] ?? null,
                    ];
                    $options = array_filter($options, static fn ($value) => $value !== null);

                    RevizySeederExtractPagesJob::dispatch($options);
                    Notification::make()
                        ->title('Pages extraction queued.')
                        ->body('Background extraction started for selected scope.')
                        ->info()
                        ->send();
                }),
            Actions\Action::make('n6_text_olmocr')
                ->label('Text (olmocr) N6')
                ->icon('heroicon-m-sparkles')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Queue N6 Text OCR (olmocr)')
                ->modalDescription('Queues N6 pages from largest to smallest with 2-minute delay. Pages already OCR-scanned for text are skipped.')
                ->action(function (): void {
                    if (WorkflowState::isPaused()) {
                        Notification::make()
                            ->title('Workflow is paused. Resume queue first.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $n6GradeIds = Grade::query()
                        ->whereIn('name', ['6', 'N6', 'Grade 6'])
                        ->pluck('id')
                        ->all();

                    $query = Page::query()
                        ->where(function ($q) use ($n6GradeIds) {
                            if ($n6GradeIds !== []) {
                                $q->whereIn('grade_id', $n6GradeIds)
                                    ->orWhere('n_p_sem', 'like', '%N6_%');
                                return;
                            }

                            $q->where('n_p_sem', 'like', '%N6_%');
                        })
                        ->where(function ($q) {
                            $q->whereNull('ocr_olmocr_path')
                                ->orWhere('ocr_olmocr_path', '');
                        })
                        ->where(function ($q) {
                            $q->whereNull('ocr_chandra_path')
                                ->orWhere('ocr_chandra_path', '');
                        })
                        ->orderByDesc('image_size')
                        ->orderByDesc('id');

                    $pages = $query->get()
                        ->unique(fn (Page $page): string => (string) ($page->md5_checksum ?: ('id:' . $page->id)))
                        ->values();

                    if ($pages->isEmpty()) {
                        Notification::make()
                            ->title('No N6 pages pending text OCR.')
                            ->success()
                            ->send();
                        return;
                    }

                    $delayIncrementSeconds = 120;
                    $workflowQueue = WorkflowState::workflowQueue();

                    foreach ($pages as $index => $page) {
                        RevizySeederLMStudioOCRJob::dispatch($page->id, 'allenai/olmocr-2-7b', 'text_only')
                            ->onQueue($workflowQueue)
                            ->delay(now()->addSeconds($index * $delayIncrementSeconds));
                    }

                    $totalQueueSeconds = $pages->count() * $delayIncrementSeconds;
                    Notification::make()
                        ->title("Queued {$pages->count()} N6 text OCR jobs (largest first).")
                        ->body("Delay: {$delayIncrementSeconds}s between jobs. Estimated queue span: {$totalQueueSeconds}s.")
                        ->info()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
