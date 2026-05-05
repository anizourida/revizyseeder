<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilesResource\Pages;
use App\Jobs\Raiida\DownloadFileAssetJob;
use App\Jobs\Raiida\SyncFilesJob;
use App\Models\Raiida\FileAsset;
use App\Services\Raiida\SyncFilesService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FilesResource extends Resource
{
    private const ALLOWED_GRADE_CODES = ['N1', 'N2', 'N3', 'N4', 'N5', 'N6'];
    private const ALLOWED_PERIOD_CODES = ['1', '2', '3', '4', '5'];
    private const ALLOWED_WEEK_CODES = ['1', '2', '3', '4', '5', '6'];

    protected static ?string $model = FileAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Files';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'File';

    protected static ?string $pluralModelLabel = 'Files';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->paginationPageOptions([25, 50, 100, 250])
            ->defaultPaginationPageOption(50)
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('filename')
                    ->label('File')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->getStateUsing(fn (FileAsset $record): string => (string) ($record->week?->period?->subject?->grade?->code ?? '-'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->getStateUsing(fn (FileAsset $record): string => (string) ($record->week?->period?->subject?->code ?? '-'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->getStateUsing(fn (FileAsset $record): string => (string) ($record->week?->period?->code ?? '-'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('week')
                    ->label('Week')
                    ->getStateUsing(fn (FileAsset $record): string => (string) ($record->week?->code ?? '-'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('size_bytes')
                    ->label('Size')
                    ->formatStateUsing(fn (int $state): string => $state > 0
                        ? number_format($state / (1024 * 1024), 2) . ' MB'
                        : '0 MB'),
                Tables\Columns\TextColumn::make('download_state')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state, FileAsset $record): string => self::resolveStatusLabel($record))
                    ->color(fn (?string $state, FileAsset $record): string => $record->is_corrupt
                        ? 'danger'
                        : (self::isSourceUnavailable($record)
                            ? 'gray'
                        : match (self::resolveDownloadState($record)) {
                            FileAsset::DOWNLOAD_STATE_DOWNLOADING => 'warning',
                            FileAsset::DOWNLOAD_STATE_DOWNLOADED => 'success',
                            FileAsset::DOWNLOAD_STATE_FAILED => 'danger',
                            default => 'gray',
                        })),
                Tables\Columns\TextColumn::make('downloaded_at')
                    ->label('Downloaded At')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_code')
                    ->label('Grade')
                    ->options(fn (): array => self::getGradeFilterOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        $gradeCode = $data['value'] ?? null;

                        if (! $gradeCode) {
                            return $query;
                        }

                        return $query->whereHas(
                            'week.period.subject.grade',
                            fn (Builder $gradeQuery) => $gradeQuery->where('code', (string) $gradeCode)
                        );
                    }),
                Tables\Filters\SelectFilter::make('period_code')
                    ->label('Period')
                    ->options(fn (): array => self::getPeriodFilterOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        $periodCode = $data['value'] ?? null;

                        if (! $periodCode) {
                            return $query;
                        }

                        return $query->whereHas(
                            'week.period',
                            fn (Builder $periodQuery) => $periodQuery->where('code', (string) $periodCode)
                        );
                    }),
                Tables\Filters\SelectFilter::make('week_code')
                    ->label('Week')
                    ->options(fn (): array => self::getWeekFilterOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        $weekCode = $data['value'] ?? null;

                        if (! $weekCode) {
                            return $query;
                        }

                        return $query->whereHas(
                            'week',
                            fn (Builder $weekQuery) => $weekQuery->where('code', (string) $weekCode)
                        );
                    }),
                Tables\Filters\TernaryFilter::make('is_corrupt')
                    ->label('Corrupt')
                    ->attribute('is_corrupt'),
                Tables\Filters\SelectFilter::make('download_state')
                    ->label('Download Status')
                    ->options([
                        FileAsset::DOWNLOAD_STATE_PENDING => 'Pending',
                        FileAsset::DOWNLOAD_STATE_DOWNLOADING => 'Downloading (Stopped)',
                        FileAsset::DOWNLOAD_STATE_DOWNLOADED => 'Downloaded',
                        FileAsset::DOWNLOAD_STATE_FAILED => 'Failed',
                    ]),
            ], layout: FiltersLayout::Dropdown)
            ->filtersFormColumns(3)
            ->headerActions([
                Tables\Actions\Action::make('sync_from_api')
                    ->label('Fetch')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->modalHeading('Fetch')
                    ->modalDescription('Start sync and monitor progress in real time.')
                    ->modalSubmitActionLabel('Start Download')
                    ->modalCancelActionLabel('Close')
                    ->closeModalByClickingAway(false)
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->modalContent(fn () => view('filament.resources.files-resource.sync-monitor', [
                        'summary' => self::buildDownloadSummary(),
                    ]))
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
                        \Filament\Forms\Components\Toggle::make('retry_failed')
                            ->label('Retry previously failed downloads')
                            ->default(false),
                    ])
                    ->action(function (array $data, Tables\Actions\Action $action): void {
                        $summary = self::buildDownloadSummary();

                        if ((bool) ($summary['workflow_active'] ?? false)) {
                            Notification::make()
                                ->title('Fetch already running')
                                ->body('A fetch workflow is active. Watch live progress in this modal.')
                                ->warning()
                                ->send();

                            $action->halt();

                            return;
                        }

                        $user = auth()->user();
                        $retryFailed = (bool) ($data['retry_failed'] ?? false);

                        SyncFilesJob::dispatch(
                            (string) Str::uuid(),
                            $user?->id,
                            $user?->email,
                            $user?->role,
                            $retryFailed
                        );

                        Notification::make()
                            ->title('Sync started')
                            ->body('Monitor updates every 3 seconds until all downloads finish.')
                            ->success()
                            ->send();

                        $action->halt();
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_extracted')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->url(
                            fn (FileAsset $record): string => route('admin.files.preview', ['fileAsset' => $record->id]),
                            shouldOpenInNewTab: true
                        )
                        ->visible(fn (FileAsset $record): bool => self::hasPresentationPreviewTarget($record)),
                    Tables\Actions\Action::make('open_file')
                        ->label('Open')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(
                            fn (FileAsset $record): string => route('admin.files.open', ['fileAsset' => $record->id]),
                            shouldOpenInNewTab: true
                        )
                        ->visible(fn (FileAsset $record): bool => self::hasOpenTarget($record)),
                    Tables\Actions\Action::make('download_now')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                        ->action(function (FileAsset $record): void {
                            $status = app(SyncFilesService::class)->downloadExistingAsset($record);

                            if ($status === 'downloaded') {
                                Notification::make()
                                    ->title('File downloaded')
                                    ->body($record->filename)
                                    ->success()
                                    ->send();

                                return;
                            }

                            if ($status === 'skipped') {
                                Notification::make()
                                    ->title('Already downloaded')
                                    ->body($record->filename)
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Download failed')
                                ->body($record->filename)
                                ->danger()
                                ->send();
                        }),
                ])
                    ->label('Menu')
                    ->icon('heroicon-o-ellipsis-horizontal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('download_selected')
                    ->label('Download Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->action(function (Collection $records): void {
                        $service = app(SyncFilesService::class);

                        $downloaded = 0;
                        $skipped = 0;
                        $failed = 0;

                        foreach ($records as $record) {
                            $status = $service->downloadExistingAsset($record);

                            if ($status === 'downloaded') {
                                $downloaded++;
                            } elseif ($status === 'skipped') {
                                $skipped++;
                            } else {
                                $failed++;
                            }
                        }

                        Notification::make()
                            ->title('Selected files processed')
                            ->body("Downloaded: {$downloaded}, Skipped: {$skipped}, Failed: {$failed}")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return self::applyDatasetScope(parent::getEloquentQuery())
            ->with(['week.period.subject.grade']);
    }

    public static function applySearchToTableQuery(Builder $query, string $search, array $searchableColumns): Builder
    {
        $query = parent::applySearchToTableQuery($query, $search, $searchableColumns);

        if ($search) {
            $query->orderByRaw("CASE WHEN filename = ? THEN 0 ELSE 1 END", [$search]);
        }

        return $query;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['week.period.subject.grade']);
    }

    private static function resolveDownloadState(FileAsset $record): string
    {
        $state = (string) ($record->download_state ?? '');

        if ($state !== '') {
            return $state;
        }

        return $record->is_downloaded
            ? FileAsset::DOWNLOAD_STATE_DOWNLOADED
            : FileAsset::DOWNLOAD_STATE_PENDING;
    }

    private static function resolveStatusLabel(FileAsset $record): string
    {
        if ($record->is_corrupt) {
            return 'Corrupt';
        }

        if (self::isSourceUnavailable($record)) {
            return 'Not Available (404)';
        }

        return match (self::resolveDownloadState($record)) {
            FileAsset::DOWNLOAD_STATE_DOWNLOADING => 'Downloading (' . self::formatMegabytes(
                self::resolveLiveDownloadedBytes($record)
            ) . ')',
            FileAsset::DOWNLOAD_STATE_DOWNLOADED => 'Downloaded',
            FileAsset::DOWNLOAD_STATE_FAILED => 'Failed',
            default => 'Pending',
        };
    }

    private static function resolveLiveDownloadedBytes(FileAsset $record): int
    {
        $filesRoot = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
        if ($filesRoot === '') {
            return 0;
        }

        $relativePath = trim((string) ($record->local_path ?? ''), " \t\n\r\0\x0B/");
        if ($relativePath === '') {
            return 0;
        }

        $absolutePath = $filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (! is_file($absolutePath)) {
            return 0;
        }

        $size = filesize($absolutePath);

        return (is_int($size) && $size > 0) ? $size : 0;
    }

    private static function formatMegabytes(int $bytes): string
    {
        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }

    private static function isSourceUnavailable(FileAsset $record): bool
    {
        if (self::resolveDownloadState($record) !== FileAsset::DOWNLOAD_STATE_FAILED) {
            return false;
        }

        return (bool) preg_match('/HTTP\s+404/i', (string) ($record->download_error ?? ''));
    }

    private static function buildDownloadSummary(): array
    {
        $scopedQuery = self::applyDatasetScope(FileAsset::query());

        $total = (int) (clone $scopedQuery)->count();
        $downloading = (int) (clone $scopedQuery)
            ->where('download_state', FileAsset::DOWNLOAD_STATE_DOWNLOADING)
            ->count();
        $failed = (int) (clone $scopedQuery)
            ->where('download_state', FileAsset::DOWNLOAD_STATE_FAILED)
            ->count();
        $downloaded = (int) (clone $scopedQuery)
            ->where(function (Builder $query): void {
                $query
                    ->where('download_state', FileAsset::DOWNLOAD_STATE_DOWNLOADED)
                    ->orWhere(function (Builder $fallback): void {
                        $fallback
                            ->whereNull('download_state')
                            ->where('is_downloaded', true);
                    });
            })
            ->count();

        $pending = max(0, $total - $downloaded - $downloading - $failed);
        $workflowQueue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
        $downloadBatchNamePrefix = trim((string) config('raiida.sync.download_batch_name', 'revizyseeder-fetch-downloads'));
        $downloadBatchLike = ($downloadBatchNamePrefix !== '' ? $downloadBatchNamePrefix : 'revizyseeder-fetch-downloads') . '%';
        $syncJobClassLike = '%' . str_replace('\\', '\\\\', SyncFilesJob::class) . '%';
        $downloadJobClassLike = '%' . str_replace('\\', '\\\\', DownloadFileAssetJob::class) . '%';

        $syncJobsTotal = 0;
        $syncJobsPending = 0;
        $syncJobsRunning = 0;
        $downloadJobsTotal = 0;
        $downloadJobsPending = 0;
        $downloadJobsRunning = 0;
        $downloadBatchId = null;
        $downloadBatchTotal = 0;
        $downloadBatchPending = 0;
        $downloadBatchFailed = 0;
        $failedWorkflowJobs = 0;
        $failedSyncJobs = 0;
        $failedDownloadJobs = 0;

        try {
            $syncJobsQuery = DB::table('jobs')
                ->where('queue', $workflowQueue)
                ->where('payload', 'like', $syncJobClassLike);

            $syncJobsTotal = (int) (clone $syncJobsQuery)->count();
            $syncJobsPending = (int) (clone $syncJobsQuery)->whereNull('reserved_at')->count();
            $syncJobsRunning = max(0, $syncJobsTotal - $syncJobsPending);

            $downloadJobsQuery = DB::table('jobs')
                ->where('queue', $workflowQueue)
                ->where('payload', 'like', $downloadJobClassLike);

            $downloadJobsTotal = (int) (clone $downloadJobsQuery)->count();
            $downloadJobsPending = (int) (clone $downloadJobsQuery)->whereNull('reserved_at')->count();
            $downloadJobsRunning = max(0, $downloadJobsTotal - $downloadJobsPending);
        } catch (\Throwable) {
            $syncJobsTotal = 0;
            $syncJobsPending = 0;
            $syncJobsRunning = 0;
            $downloadJobsTotal = 0;
            $downloadJobsPending = 0;
            $downloadJobsRunning = 0;
        }

        try {
            $failedWorkflowJobs = (int) DB::table('failed_jobs')
                ->where('queue', $workflowQueue)
                ->count();

            $failedSyncJobs = (int) DB::table('failed_jobs')
                ->where('queue', $workflowQueue)
                ->where('payload', 'like', $syncJobClassLike)
                ->count();

            $failedDownloadJobs = (int) DB::table('failed_jobs')
                ->where('queue', $workflowQueue)
                ->where('payload', 'like', $downloadJobClassLike)
                ->count();
        } catch (\Throwable) {
            $failedWorkflowJobs = 0;
            $failedSyncJobs = 0;
            $failedDownloadJobs = 0;
        }

        try {
            $activeBatch = DB::table('job_batches')
                ->select(['id', 'total_jobs', 'pending_jobs', 'failed_jobs'])
                ->where('name', 'like', $downloadBatchLike)
                ->whereNull('finished_at')
                ->orderByDesc('created_at')
                ->first();

            if ($activeBatch) {
                $downloadBatchId = (string) $activeBatch->id;
                $downloadBatchTotal = (int) $activeBatch->total_jobs;
                $downloadBatchPending = (int) $activeBatch->pending_jobs;
                $downloadBatchFailed = (int) $activeBatch->failed_jobs;

                if ($downloadBatchPending > 0 && $downloadJobsTotal === 0 && $syncJobsTotal === 0) {
                    $downloadBatchPending = 0;
                }
            }
        } catch (\Throwable) {
            $downloadBatchId = null;
            $downloadBatchTotal = 0;
            $downloadBatchPending = 0;
            $downloadBatchFailed = 0;
        }

        $activeDownloadedBytes = self::resolveActiveDownloadedBytes();
        $totalDownloadedBytes = (int) (clone $scopedQuery)
            ->where('is_downloaded', true)
            ->sum('size_bytes');

        $queuedJobs = $syncJobsPending + max($downloadJobsPending, $downloadBatchPending);
        $runningJobs = $syncJobsRunning + $downloadJobsRunning;
        $hasQueueWork = ($syncJobsTotal + $downloadJobsTotal + $downloadBatchPending) > 0;
        $isActiveTransfer = ($runningJobs > 0)
            || ($hasQueueWork && (($downloading > 0) || ($activeDownloadedBytes > 0)));
        $isQueueWaiting = ($queuedJobs > 0) && (! $isActiveTransfer);
        $completed = min($total, $downloaded + $failed);

        $progress = $total > 0
            ? (int) floor(($completed / max(1, $total)) * 100)
            : (($syncJobsTotal + $downloadJobsTotal) > 0 ? 0 : 100);

        $allDownloaded = $total > 0
            && $downloaded === $total
            && $pending === 0
            && $downloading === 0
            && $queuedJobs === 0
            && $runningJobs === 0
            && $failed === 0;

        if ($allDownloaded) {
            $statusMessage = 'All downloaded.';
        } elseif ($isActiveTransfer) {
            $statusMessage = 'Downloading in progress...';
        } elseif ($isQueueWaiting) {
            $statusMessage = 'Queued and waiting for queue worker...';
        } elseif ($downloading > 0) {
            $statusMessage = 'Download appears interrupted. Click Start Download to resume.';
        } elseif ($pending > 0) {
            $statusMessage = 'Pending files are ready to download.';
        } elseif ($failed > 0) {
            $statusMessage = 'Download finished with failed files.';
        } else {
            $statusMessage = 'No files discovered yet. Click Start Download.';
        }

        return [
            'status_message' => $statusMessage,
            'all_downloaded' => $allDownloaded,
            'workflow_active' => ($isActiveTransfer || $isQueueWaiting || $syncJobsRunning > 0),
            'total' => $total,
            'downloaded' => $downloaded,
            'downloading' => $downloading,
            'pending' => $pending,
            'failed' => $failed,
            'progress' => max(0, min(100, $progress)),
            'remaining' => max(0, $total - $downloaded),
            'queued_jobs' => $queuedJobs,
            'sync_jobs_total' => $syncJobsTotal,
            'sync_jobs_pending' => $syncJobsPending,
            'sync_jobs_running' => $syncJobsRunning,
            'download_jobs_total' => $downloadJobsTotal,
            'download_jobs_pending' => $downloadJobsPending,
            'download_jobs_running' => $downloadJobsRunning,
            'download_batch_id' => $downloadBatchId,
            'download_batch_total' => $downloadBatchTotal,
            'download_batch_pending' => $downloadBatchPending,
            'download_batch_failed' => $downloadBatchFailed,
            'queue_waiting' => $isQueueWaiting,
            'active_transfer' => $isActiveTransfer,
            'failed_workflow_jobs' => $failedWorkflowJobs,
            'failed_sync_jobs' => $failedSyncJobs,
            'failed_download_jobs' => $failedDownloadJobs,
            'workflow_queue' => $workflowQueue,
            'active_downloaded_bytes' => $activeDownloadedBytes,
            'active_downloaded_kb' => round($activeDownloadedBytes / 1024, 1),
            'total_downloaded_bytes' => $totalDownloadedBytes,
            'total_downloaded_kb' => round($totalDownloadedBytes / 1024, 1),
        ];
    }

    private static function resolveActiveDownloadedBytes(): int
    {
        $filesRoot = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
        if ($filesRoot === '') {
            return 0;
        }

        $relativePaths = self::applyDatasetScope(FileAsset::query())
            ->where('download_state', FileAsset::DOWNLOAD_STATE_DOWNLOADING)
            ->whereNotNull('local_path')
            ->pluck('local_path');

        $bytes = 0;

        foreach ($relativePaths as $relativePath) {
            $relativePath = trim((string) $relativePath, " \t\n\r\0\x0B/");
            if ($relativePath === '') {
                continue;
            }

            $absolutePath = $filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            if (! is_file($absolutePath)) {
                continue;
            }

            $size = filesize($absolutePath);
            if (is_int($size) && $size > 0) {
                $bytes += $size;
            }
        }

        return $bytes;
    }

    public static function getGradeFilterOptions(): array
    {
        return array_combine(self::ALLOWED_GRADE_CODES, self::ALLOWED_GRADE_CODES);
    }

    public static function getPeriodFilterOptions(): array
    {
        return array_combine(self::ALLOWED_PERIOD_CODES, self::ALLOWED_PERIOD_CODES);
    }

    public static function getWeekFilterOptions(): array
    {
        return array_combine(self::ALLOWED_WEEK_CODES, self::ALLOWED_WEEK_CODES);
    }

    private static function hasOpenTarget(FileAsset $record): bool
    {
        return trim((string) $record->local_path, " \t\n\r\0\x0B/") !== ''
            || filter_var((string) $record->original_url, FILTER_VALIDATE_URL) !== false;
    }

    private static function hasPresentationPreviewTarget(FileAsset $record): bool
    {
        if ($record->is_presentation_data_extracted) {
            return true;
        }

        return trim((string) $record->presentation_json_path) !== '';
    }

    private static function applyDatasetScope(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'week.period.subject.grade',
                fn (Builder $gradeQuery) => $gradeQuery->whereIn('code', self::ALLOWED_GRADE_CODES)
            )
            ->whereHas(
                'week.period.subject',
                fn (Builder $subjectQuery) => $subjectQuery->where('code', 'FR')
            )
            ->whereHas(
                'week.period',
                fn (Builder $periodQuery) => $periodQuery->whereIn('code', self::ALLOWED_PERIOD_CODES)
            )
            ->whereHas(
                'week',
                fn (Builder $weekQuery) => $weekQuery->whereIn('code', self::ALLOWED_WEEK_CODES)
            );
    }
}
