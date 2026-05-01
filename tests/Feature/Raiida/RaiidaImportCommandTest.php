<?php

namespace Tests\Feature\Raiida;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PDO;
use Tests\TestCase;

class RaiidaImportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_command_preserves_source_table_counts(): void
    {
        $source = (string) config('raiida.source_sqlite_path');
        if (! file_exists($source)) {
            $this->markTestSkipped('Source raiida.db is not available.');
        }

        $this->artisan('raiida:import', ['--source' => $source])->assertExitCode(0);

        $pdo = new PDO('sqlite:' . $source);
        $map = [
            'grade' => 'grades',
            'subject' => 'subjects',
            'period' => 'periods',
            'week' => 'weeks',
            'fileasset' => 'file_assets',
            'vocabularyitem' => 'vocabulary_items',
            'audio' => 'audios',
            'conjugaison' => 'conjugaisons',
            'grammaire' => 'grammaires',
            'questionpublishattempt' => 'question_publish_attempts',
        ];

        foreach ($map as $sourceTable => $targetTable) {
            $sourceCount = (int) $pdo->query("SELECT COUNT(*) FROM {$sourceTable}")->fetchColumn();
            $targetCount = DB::table($targetTable)->count();

            $this->assertSame(
                $sourceCount,
                $targetCount,
                "Count mismatch for source {$sourceTable} -> target {$targetTable}"
            );
        }
    }
}
