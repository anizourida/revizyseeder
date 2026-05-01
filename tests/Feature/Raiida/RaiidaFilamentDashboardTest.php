<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use App\Models\Raiida\Subject;
use App\Models\Raiida\Week;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class RaiidaFilamentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_dashboard_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_operator_can_access_filament_dashboard(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('RevizySeeder');
    }

    public function test_operator_can_access_files_resource_page(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-files@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $this->actingAs($user)
            ->get('/admin/files')
            ->assertOk()
            ->assertSee('Files')
            ->assertSee('tableFilters.grade_code.value')
            ->assertSee('tableFilters.period_code.value')
            ->assertSee('tableFilters.week_code.value')
            ->assertDontSee('tableFilters.subject_id.value');
    }

    public function test_operator_can_access_conjugaison_resource_page(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-conjugaison@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $this->actingAs($user)
            ->get('/admin/conjugaisons')
            ->assertOk()
            ->assertSee('Conjugaison Lessons')
            ->assertSee('tableFilters.grade_id.value')
            ->assertSee('tableFilters.period_id.value')
            ->assertSee('tableFilters.week_id.value');
    }

    public function test_files_resource_shows_not_available_badge_for_http_404_failures(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-unavailable@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $week = $this->createWeekHierarchy('N4', 'FR', '2', '6');

        FileAsset::query()->create([
            'filename' => 'FR_N4_P2_SEM6_S2.pptx',
            'week_id' => $week->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_FAILED,
            'download_progress' => 0,
            'download_error' => 'HTTP 404',
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get('/admin/files')
            ->assertOk()
            ->assertSee('Not Available (404)');
    }

    public function test_files_resource_shows_live_mb_for_downloading_status(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-downloading@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $filesRoot = storage_path('app/testing-filament-live-mb');
        File::deleteDirectory($filesRoot);
        File::ensureDirectoryExists($filesRoot);
        config()->set('raiida.files_root', $filesRoot);

        $relativePath = 'FR/niveau_4/periode_1/semaine_1/FR_N4_P1_SEM1_S1.pptx';
        $absolutePath = $filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('x', 3 * 1024 * 1024));

        $week = $this->createWeekHierarchy('N4', 'FR', '1', '1');

        FileAsset::query()->create([
            'filename' => 'FR_N4_P1_SEM1_S1.pptx',
            'local_path' => $relativePath,
            'week_id' => $week->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADING,
            'download_progress' => 30,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get('/admin/files')
            ->assertOk()
            ->assertSee('Downloading (3.00 MB)');

        File::deleteDirectory($filesRoot);
    }

    public function test_files_resource_shows_only_fr_n1_to_n6_period_1_to_5_week_1_to_6(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-scope@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $allowedWeek = $this->createWeekHierarchy('N1', 'FR', '1', '1');
        $disallowedSubjectWeek = $this->createWeekHierarchy('N1', 'AR', '1', '1');
        $disallowedGradeWeek = $this->createWeekHierarchy('N100', 'FR', '1', '1');
        $disallowedPeriodWeek = $this->createWeekHierarchy('N1', 'FR', '7', '1');
        $disallowedWeekWeek = $this->createWeekHierarchy('N1', 'FR', '1', '7');

        FileAsset::query()->create([
            'filename' => 'allowed_file.pptx',
            'week_id' => $allowedWeek->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        FileAsset::query()->create([
            'filename' => 'blocked_subject_file.pptx',
            'week_id' => $disallowedSubjectWeek->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        FileAsset::query()->create([
            'filename' => 'blocked_grade_file.pptx',
            'week_id' => $disallowedGradeWeek->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        FileAsset::query()->create([
            'filename' => 'blocked_period_file.pptx',
            'week_id' => $disallowedPeriodWeek->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        FileAsset::query()->create([
            'filename' => 'blocked_week_file.pptx',
            'week_id' => $disallowedWeekWeek->id,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get('/admin/files')
            ->assertOk()
            ->assertSee('allowed_file.pptx')
            ->assertDontSee('blocked_subject_file.pptx')
            ->assertDontSee('blocked_grade_file.pptx')
            ->assertDontSee('blocked_period_file.pptx')
            ->assertDontSee('blocked_week_file.pptx');
    }

    public function test_authenticated_user_can_open_local_file_from_admin_open_route(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-open-local@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $filesRoot = storage_path('app/testing-filament-open-local');
        File::deleteDirectory($filesRoot);
        File::ensureDirectoryExists($filesRoot);
        config()->set('raiida.files_root', $filesRoot);

        $relativePath = 'FR/niveau_4/periode_1/semaine_1/FR_N4_P1_SEM1_S1.pptx';
        $absolutePath = $filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('x', 1024));

        $record = FileAsset::query()->create([
            'filename' => 'FR_N4_P1_SEM1_S1.pptx',
            'local_path' => $relativePath,
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'download_error' => null,
            'size_bytes' => 1024,
            'vocab_count' => 0,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.files.open', ['fileAsset' => $record->id]));

        $response
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="FR_N4_P1_SEM1_S1.pptx"');

        File::deleteDirectory($filesRoot);
    }

    public function test_open_route_redirects_to_original_url_when_local_file_is_missing(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-open-redirect@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $filesRoot = storage_path('app/testing-filament-open-redirect-empty');
        File::deleteDirectory($filesRoot);
        File::ensureDirectoryExists($filesRoot);
        config()->set('raiida.files_root', $filesRoot);

        $record = FileAsset::query()->create([
            'filename' => 'FR_N4_P2_SEM1_S1.pptx',
            'local_path' => 'FR/niveau_4/periode_2/semaine_1/FR_N4_P2_SEM1_S1.pptx',
            'original_url' => 'https://example.com/FR_N4_P2_SEM1_S1.pptx',
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('admin.files.open', ['fileAsset' => $record->id]))
            ->assertRedirect('https://example.com/FR_N4_P2_SEM1_S1.pptx');

        File::deleteDirectory($filesRoot);
    }

    public function test_authenticated_user_can_open_extracted_presentation_preview(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-preview@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $lessonId = 'FR_N4_P1_SEM1_S1';
        $presentationDir = storage_path('app/presentation_data/' . $lessonId);
        $assetsDir = $presentationDir . '/assets';

        File::deleteDirectory($presentationDir);
        File::ensureDirectoryExists($assetsDir);
        File::put($assetsDir . '/slide_1_image_1.png', 'fake-image');

        $jsonPath = $presentationDir . '/data.json';
        File::put($jsonPath, json_encode([
            'file_name' => $lessonId . '.pptx',
            'lesson_id' => $lessonId,
            'metadata' => [
                'total_slides' => 1,
                'slide_width_emu' => 12192000,
                'slide_height_emu' => 6858000,
            ],
            'slides' => [
                [
                    'id' => 1,
                    'elements' => [
                        [
                            'type' => 'text',
                            'content' => 'Bonjour',
                            'bbox' => [609600, 457200, 2438400, 609600],
                        ],
                        [
                            'type' => 'image',
                            'file_path' => 'assets/slide_1_image_1.png',
                            'bbox' => [3048000, 1143000, 1828800, 1828800],
                            'description' => 'Image 1',
                        ],
                    ],
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $record = FileAsset::query()->create([
            'filename' => $lessonId . '.pptx',
            'local_path' => 'FR/niveau_4/periode_1/semaine_1/' . $lessonId . '.pptx',
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'is_presentation_data_extracted' => true,
            'presentation_json_path' => 'storage/app/presentation_data/' . $lessonId . '/data.json',
            'presentation_assets_dir' => 'storage/app/presentation_data/' . $lessonId . '/assets',
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('admin.files.preview', ['fileAsset' => $record->id]))
            ->assertOk()
            ->assertSee('Extracted Preview')
            ->assertSee('Slide 1')
            ->assertSee('Bonjour')
            ->assertSee('/admin/files/preview/' . $record->id . '/asset/assets/slide_1_image_1.png');

        File::deleteDirectory($presentationDir);
    }

    public function test_preview_asset_route_serves_extracted_files(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'filament-operator-preview-asset@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        $lessonId = 'FR_N4_P1_SEM2_S1';
        $presentationDir = storage_path('app/presentation_data/' . $lessonId);
        $assetsDir = $presentationDir . '/assets';

        File::deleteDirectory($presentationDir);
        File::ensureDirectoryExists($assetsDir);
        File::put($assetsDir . '/slide_1_video_1.mp4', 'fake-video');
        File::put($presentationDir . '/data.json', json_encode([
            'file_name' => $lessonId . '.pptx',
            'lesson_id' => $lessonId,
            'metadata' => ['total_slides' => 1],
            'slides' => [],
        ], JSON_PRETTY_PRINT));

        $record = FileAsset::query()->create([
            'filename' => $lessonId . '.pptx',
            'local_path' => 'FR/niveau_4/periode_1/semaine_2/' . $lessonId . '.pptx',
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'is_presentation_data_extracted' => true,
            'presentation_json_path' => 'storage/app/presentation_data/' . $lessonId . '/data.json',
            'presentation_assets_dir' => 'storage/app/presentation_data/' . $lessonId . '/assets',
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('admin.files.preview.asset', [
                'fileAsset' => $record->id,
                'assetPath' => 'assets/slide_1_video_1.mp4',
            ]))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="slide_1_video_1.mp4"');

        File::deleteDirectory($presentationDir);
    }

    private function createWeekHierarchy(
        string $gradeCode,
        string $subjectCode,
        string $periodCode,
        string $weekCode
    ): Week {
        $grade = Grade::query()->firstOrCreate([
            'code' => $gradeCode,
        ], [
            'name' => $gradeCode,
        ]);

        $subject = Subject::query()->firstOrCreate([
            'grade_id' => $grade->id,
            'code' => $subjectCode,
        ], [
            'name' => $subjectCode,
        ]);

        $period = Period::query()->firstOrCreate([
            'subject_id' => $subject->id,
            'code' => $periodCode,
        ], [
            'name' => $periodCode,
        ]);

        return Week::query()->firstOrCreate([
            'period_id' => $period->id,
            'code' => $weekCode,
        ], [
            'name' => $weekCode,
        ]);
    }
}
