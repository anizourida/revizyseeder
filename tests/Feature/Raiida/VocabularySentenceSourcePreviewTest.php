<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use App\Models\Raiida\VocabularySentence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class VocabularySentenceSourcePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_vocabulary_sentence_resolves_file_asset_and_preview_url(): void
    {
        $lessonId = 'FR_N1_P1_SEM1_S1';
        $presentationDir = storage_path('app/presentation_data/' . $lessonId);
        File::makeDirectory($presentationDir . '/assets', 0755, true, true);

        File::put(
            $presentationDir . '/data.json',
            json_encode([
                'metadata' => [
                    'slide_width_emu' => 12192000,
                    'slide_height_emu' => 6858000,
                ],
                'slides' => [
                    [
                        'id' => 6,
                        'elements' => [
                            [
                                'type' => 'text',
                                'content' => 'Bonjour les enfants ! Bienvenue dans la classe.',
                                'bbox' => [1692000, 756000, 6840000, 612000],
                            ],
                        ],
                    ],
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $fileAsset = FileAsset::query()->create([
            'filename' => $lessonId . '.ppsx',
            'is_downloaded' => true,
            'is_presentation_data_extracted' => true,
            'presentation_slide_count' => 1,
            'presentation_json_path' => 'storage/app/presentation_data/' . $lessonId . '/data.json',
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
        ]);

        $vocabItem = VocabularyItem::query()->create([
            'word' => 'Bonjour',
            'grade' => 'N1',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => $lessonId,
        ]);

        $sentence = VocabularySentence::query()->create([
            'vocabulary_item_id' => $vocabItem->id,
            'file_asset_id' => $fileAsset->id,
            'word' => 'Bonjour',
            'grade' => 'N1',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => $lessonId,
            'sentence' => 'Bonjour les enfants ! Bienvenue dans la classe.',
            'source_session' => 'S1',
            'source_slide' => 6,
            'source_type' => 'slide',
        ]);

        $this->assertNotNull($sentence->preview_url);
        $this->assertStringContainsString('/admin/files/preview/' . $fileAsset->id, $sentence->preview_url);
        $this->assertStringContainsString('slide=6', $sentence->preview_url);

        $user = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin-vocab-preview@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($user)->get($sentence->preview_url);
        $response->assertOk()
            ->assertSee('FR_N1_P1_SEM1_S1.ppsx')
            ->assertSee('Slide 6')
            ->assertSee('Bonjour les enfants ! Bienvenue dans la classe.');

        File::deleteDirectory($presentationDir);
    }
}
