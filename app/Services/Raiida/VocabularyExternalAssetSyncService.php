<?php

namespace App\Services\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Models\Raiida\VocabularyBaseWordAudio;
use App\Services\Raiida\External\RevizySystemClient;
use App\Services\Raiida\External\WalidioClient;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class VocabularyExternalAssetSyncService
{
    public function __construct(
        private readonly MediaFileLocator $locator,
        private readonly RevizySystemClient $revizy,
        private readonly WalidioClient $walidio
    ) {
    }

    /**
     * @param  array{
     *   limit?:int,
     *   grade?:string,
     *   period?:string,
     *   week?:string,
     *   sync_image_revizy?:bool,
     *   sync_audio_revizy?:bool,
     *   sync_base_word_audio_revizy?:bool,
     *   sync_image_walidio?:bool,
     *   only_missing?:bool,
     *   wait_ms?:int
     * }  $options
     * @return array<string,mixed>
     */
    public function syncBatch(array $options = []): array
    {
        $syncImageRevizy = (bool) ($options['sync_image_revizy'] ?? true);
        $syncAudioRevizy = (bool) ($options['sync_audio_revizy'] ?? true);
        $syncBaseWordAudioRevizy = (bool) ($options['sync_base_word_audio_revizy'] ?? true);
        $syncImageWalidio = (bool) ($options['sync_image_walidio'] ?? true);
        $onlyMissing = (bool) ($options['only_missing'] ?? true);
        $limit = max(1, min((int) ($options['limit'] ?? 5000), 50000));
        $waitMs = max(0, min((int) ($options['wait_ms'] ?? 0), 5000));

        $query = VocabularyItem::query()
            ->select([
                'id',
                'word',
                'base_word',
                'grade',
                'period',
                'week',
                'image_path',
                'audio_path',
                'base_word_audio_path',
                'revizy_image_file_id',
                'revizy_audio_file_id',
                'walidio_image_id',
            ])
            ->with(['baseWordAudio:id,vocabulary_item_id,revizy_file_id'])
            ->orderBy('id');

        $this->applyScopeFilters($query, $options);

        $summary = [
            'targeted' => 0,
            'processed_total' => 0,
            'failed_total' => 0,
            'revizy_image_synced' => 0,
            'revizy_audio_synced' => 0,
            'revizy_base_word_audio_synced' => 0,
            'walidio_image_synced' => 0,
            'walidio_blocked_missing_revizy_image' => 0,
            'walidio_skipped_config' => 0,
            'errors' => [],
        ];

        $walidioConfigured = $this->isWalidioConfigured();
        $query->chunkById(100, function ($items) use (
            $limit,
            &$summary,
            $syncImageRevizy,
            $syncAudioRevizy,
            $syncBaseWordAudioRevizy,
            $syncImageWalidio,
            $onlyMissing,
            $waitMs,
            $walidioConfigured
        ) {
            foreach ($items as $item) {
                if ($summary['processed_total'] >= $limit) {
                    return false;
                }

                $summary['processed_total']++;

                try {
                    if ($syncImageRevizy) {
                        $result = $this->syncImageToRevizy($item, $onlyMissing);
                        if ($result === 'synced') {
                            $summary['revizy_image_synced']++;
                        }
                    }

                    if ($syncAudioRevizy) {
                        $result = $this->syncAudioToRevizy($item, $onlyMissing);
                        if ($result === 'synced') {
                            $summary['revizy_audio_synced']++;
                        }
                    }

                    if ($syncBaseWordAudioRevizy) {
                        $result = $this->syncBaseWordAudioToRevizy($item, $onlyMissing);
                        if ($result === 'synced') {
                            $summary['revizy_base_word_audio_synced']++;
                        }
                    }

                    if ($syncImageWalidio) {
                        if (! $walidioConfigured) {
                            $summary['walidio_skipped_config']++;
                        } else {
                            $result = $this->syncImageToWalidio($item, $onlyMissing);
                            if ($result === 'blocked') {
                                $summary['walidio_blocked_missing_revizy_image']++;
                            } elseif ($result === 'synced') {
                                $summary['walidio_image_synced']++;
                            }
                        }
                    }
                } catch (Throwable $exception) {
                    $summary['failed_total']++;
                    $this->pushError(
                        $summary['errors'],
                        "#{$item->id} {$item->word}: {$exception->getMessage()}"
                    );
                }

                if ($waitMs > 0) {
                    usleep($waitMs * 1000);
                }
            }
        });

        $summary['targeted'] = $summary['processed_total'];

        return $summary;
    }

    private function syncBaseWordAudioToRevizy(VocabularyItem $item, bool $onlyMissing): string
    {
        $baseWord = trim((string) ($item->base_word ?? ''));
        if ($baseWord === '' || mb_strtolower($baseWord, 'UTF-8') === mb_strtolower((string) $item->word, 'UTF-8')) {
            return 'blocked';
        }

        if ($onlyMissing && $item->baseWordAudio instanceof VocabularyBaseWordAudio) {
            $existing = trim((string) ($item->baseWordAudio->revizy_file_id ?? ''));
            if ($existing !== '') {
                return 'already';
            }
        }

        if (trim((string) $item->base_word_audio_path) === '') {
            throw new \RuntimeException('No base_word audio associated with this asset.');
        }

        $path = $this->locator->resolveBaseWordAudioPath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('Base word audio file not found: ' . (string) $item->base_word_audio_path);
        }

        $response = $this->revizy->uploadFile($path, $baseWord ?: 'Base word audio ' . $item->id);
        $secret = trim((string) ($response['secret_id'] ?? ''));
        if ($secret === '') {
            throw new \RuntimeException('Revizy response missing secret_id for base word audio upload.');
        }

        VocabularyBaseWordAudio::query()->updateOrCreate(
            ['vocabulary_item_id' => (int) $item->id],
            ['revizy_file_id' => $secret]
        );

        return 'synced';
    }

    private function syncImageToRevizy(VocabularyItem $item, bool $onlyMissing): string
    {
        if ($onlyMissing && trim((string) $item->revizy_image_file_id) !== '') {
            return 'already';
        }

        if (trim((string) $item->image_path) === '') {
            throw new \RuntimeException('No image associated with this asset.');
        }

        $path = $this->locator->resolveImagePath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('Image file not found: ' . (string) $item->image_path);
        }

        $response = $this->revizy->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
        $secret = trim((string) ($response['secret_id'] ?? ''));
        if ($secret === '') {
            throw new \RuntimeException('Revizy response missing secret_id for image upload.');
        }

        $item->revizy_image_file_id = $secret;
        $item->save();

        return 'synced';
    }

    private function syncAudioToRevizy(VocabularyItem $item, bool $onlyMissing): string
    {
        if ($onlyMissing && trim((string) $item->revizy_audio_file_id) !== '') {
            return 'already';
        }

        if (trim((string) $item->audio_path) === '') {
            throw new \RuntimeException('No audio associated with this asset.');
        }

        $path = $this->locator->resolveAudioPath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('Audio file not found: ' . (string) $item->audio_path);
        }

        $response = $this->revizy->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
        $secret = trim((string) ($response['secret_id'] ?? ''));
        if ($secret === '') {
            throw new \RuntimeException('Revizy response missing secret_id for audio upload.');
        }

        $item->revizy_audio_file_id = $secret;
        $item->save();

        return 'synced';
    }

    private function syncImageToWalidio(VocabularyItem $item, bool $onlyMissing): string
    {
        if ($onlyMissing && trim((string) $item->walidio_image_id) !== '') {
            return 'already';
        }

        if (trim((string) $item->revizy_image_file_id) === '') {
            return 'blocked';
        }

        if (trim((string) $item->image_path) === '') {
            throw new \RuntimeException('No image associated with this asset.');
        }

        $path = $this->locator->resolveImagePath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('Image file not found: ' . (string) $item->image_path);
        }

        $payload = $this->walidio->uploadImage($path, [
            'name' => $item->word ?: 'Asset ' . $item->id,
            'n' => $item->grade,
            'p' => $item->period,
            'sem' => $item->week,
            'revizy_file_id' => $item->revizy_image_file_id,
        ]);

        $walidioId = null;
        if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data']['id'])) {
            $walidioId = trim((string) $payload['data']['id']);
        } elseif (isset($payload['id'])) {
            $walidioId = trim((string) $payload['id']);
        }

        if (! is_string($walidioId) || $walidioId === '') {
            throw new \RuntimeException('Walidio response missing ID.');
        }

        $item->walidio_image_id = $walidioId;
        $item->save();

        return 'synced';
    }

    /**
     * @param  Builder<VocabularyItem>  $query
     * @param  array<string,mixed>  $options
     */
    private function applyScopeFilters(Builder $query, array $options): void
    {
        if (! empty($options['grade'])) {
            $query->where('grade', strtoupper(trim((string) $options['grade'])));
        }
        if (! empty($options['period'])) {
            $query->where('period', strtoupper(trim((string) $options['period'])));
        }
        if (! empty($options['week'])) {
            $query->where('week', strtoupper(trim((string) $options['week'])));
        }
    }

    private function isWalidioConfigured(): bool
    {
        $publicKey = trim((string) config('raiida.walidio.public_key', ''));
        $baseUrl = trim((string) config('raiida.walidio.base_url', ''));

        return $publicKey !== '' && $baseUrl !== '';
    }

    /**
     * @param  array<int,string>  $errors
     */
    private function pushError(array &$errors, string $message): void
    {
        if (count($errors) >= 20) {
            if (($errors[19] ?? null) !== 'More errors omitted...') {
                $errors[19] = 'More errors omitted...';
            }

            return;
        }

        $errors[] = $message;
    }
}
