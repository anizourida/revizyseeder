<?php

namespace Tests\Feature\Raiida;

use App\Jobs\Raiida\ExtractVocabularyJob;
use App\Jobs\Raiida\InspectFilesJob;
use App\Jobs\Raiida\SyncFilesJob;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class RaiidaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviewer_is_forbidden_from_mutation_endpoints(): void
    {
        $reviewer = User::query()->create([
            'name' => 'Reviewer',
            'email' => 'reviewer@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_REVIEWER,
        ]);

        Sanctum::actingAs($reviewer);

        $this->postJson('/api/sync')->assertForbidden();
        $this->postJson('/api/inspect')->assertForbidden();
        $this->postJson('/api/extract-vocabulary')->assertForbidden();
        $this->postJson('/api/extract-vocabulary/1')->assertForbidden();
        $this->postJson('/api/sync-assets')->assertForbidden();
        $this->postJson('/api/batch-generate-publish')->assertForbidden();
        $this->postJson('/api/questions/0/unaccept', [
            'concept_id' => '100',
            'name' => 'Q',
            'data' => [],
        ])->assertForbidden();
        $this->deleteJson('/api/questions/1')->assertForbidden();
    }

    public function test_operator_has_access_only_to_non_admin_mutations(): void
    {
        $operator = User::query()->create([
            'name' => 'Operator',
            'email' => 'operator-authz@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        Sanctum::actingAs($operator);

        $attempt = QuestionPublishAttempt::query()->create([
            'local_question_id' => 4,
            'concept_id' => '901',
            'name' => 'Delete me',
            'question_data' => '{}',
            'status' => 'unaccepted',
        ]);

        $this->postJson('/api/sync-assets')->assertOk();
        $this->postJson('/api/questions/0/unaccept', [
            'concept_id' => '100',
            'name' => 'Q',
            'data' => ['instruction' => 'x', 'answers' => []],
        ])->assertOk();
        $this->deleteJson('/api/questions/' . $attempt->id)->assertOk();

        $this->postJson('/api/sync')->assertForbidden();
        $this->postJson('/api/inspect')->assertForbidden();
        $this->postJson('/api/extract-vocabulary')->assertForbidden();
        $this->postJson('/api/extract-vocabulary/1')->assertForbidden();
        $this->postJson('/api/batch-generate-publish')->assertForbidden();
    }

    public function test_admin_can_execute_admin_only_mutation_endpoints(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-authz@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        Sanctum::actingAs($admin);
        Queue::fake();

        $fileAsset = FileAsset::query()->create([
            'filename' => 'FR_N1_P1_SEM1_S1.pptx',
            'local_path' => 'nonexistent/path/file.pptx',
            'original_url' => 'https://example.com/file.pptx',
            'size_bytes' => 0,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'vocab_count' => 0,
        ]);

        $this->postJson('/api/sync')
            ->assertOk()
            ->assertJson(['message' => 'Sync started in background']);
        $this->postJson('/api/inspect')
            ->assertOk()
            ->assertJson(['message' => 'Inspection started in background']);
        $this->postJson('/api/extract-vocabulary')
            ->assertOk()
            ->assertJson(['message' => 'Vocabulary extraction started in background']);
        $this->postJson('/api/extract-vocabulary/' . $fileAsset->id)
            ->assertOk();
        $this->postJson('/api/batch-generate-publish')
            ->assertOk();

        Queue::assertPushed(SyncFilesJob::class);
        Queue::assertPushed(InspectFilesJob::class);
        Queue::assertPushed(ExtractVocabularyJob::class);
    }

    public function test_admin_mutation_routes_emit_audit_logs_with_context_id(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-audit@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        Sanctum::actingAs($admin);

        $contextId = 'ctx-admin-audit-001';

        Log::shouldReceive('info')
            ->once()
            ->with(
                'raiida.admin_mutation.start',
                Mockery::on(function (array $context) use ($contextId, $admin): bool {
                    return ($context['workflow_context_id'] ?? null) === $contextId
                        && ($context['user_id'] ?? null) === $admin->id
                        && ($context['user_role'] ?? null) === User::ROLE_ADMIN
                        && ($context['path'] ?? null) === '/api/batch-generate-publish';
                })
            );

        Log::shouldReceive('info')
            ->once()
            ->with(
                'raiida.admin_mutation.completed',
                Mockery::on(function (array $context) use ($contextId, $admin): bool {
                    return ($context['workflow_context_id'] ?? null) === $contextId
                        && ($context['user_id'] ?? null) === $admin->id
                        && ($context['status_code'] ?? null) === 200;
                })
            );

        $this->withHeader('X-Workflow-Context-Id', $contextId)
            ->postJson('/api/batch-generate-publish')
            ->assertOk()
            ->assertHeader('X-Workflow-Context-Id', $contextId);
    }
}
