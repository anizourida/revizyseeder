<?php

namespace App\Services\Raiida;

use App\Models\Raiida\Audio;
use App\Models\Raiida\VocabularyItem;

class AssetSyncService
{
    public function syncAudioPaths(): array
    {
        $updated = 0;
        $total = VocabularyItem::query()->count();

        VocabularyItem::query()->orderBy('id')->chunkById(200, function ($items) use (&$updated): void {
            foreach ($items as $item) {
                $audioPath = Audio::query()
                    ->where('vocabulary_item_id', $item->id)
                    ->value('file_path');

                if ($item->audio_path !== $audioPath) {
                    $item->audio_path = $audioPath;
                    $item->save();
                    $updated++;
                }
            }
        });

        return [
            'updated' => $updated,
            'total' => $total,
        ];
    }
}
