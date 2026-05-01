<?php

use App\Http\Controllers\Api\Raiida\FileController;
use App\Http\Controllers\Api\Raiida\QuestionGenerationController;
use App\Http\Controllers\Api\Raiida\QuestionController;
use App\Http\Controllers\Api\Raiida\QuestionPublishController;
use App\Http\Controllers\Api\Raiida\StatsController;
use App\Http\Controllers\Api\Raiida\AuthController;
use App\Http\Controllers\Api\Raiida\AudioController;
use App\Http\Controllers\Api\Raiida\ApiProviderController;
use App\Http\Controllers\Api\Raiida\ConceptController;
use App\Http\Controllers\Api\Raiida\IntegrityController;
use App\Http\Controllers\Api\Raiida\ExternalAssetSyncController;
use App\Http\Controllers\Api\Raiida\FlashcardController;
use App\Http\Controllers\Api\Raiida\RevizyProxyController;
use App\Http\Controllers\Api\Raiida\SyncController;
use App\Http\Controllers\Api\Raiida\ConjugaisonController;
use App\Http\Controllers\Api\Raiida\GrammaireController;
use App\Http\Controllers\Api\Raiida\RoadmapController;
use App\Http\Controllers\Api\Raiida\VocabularyExtractionController;
use App\Http\Controllers\Api\Raiida\VocabularyMetadataController;
use App\Http\Controllers\Api\Raiida\VocabularyAssetController;
use App\Http\Controllers\Api\Raiida\VocabularyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/files', [FileController::class, 'index']);
    Route::get('/tree', [FileController::class, 'tree']);

    Route::get('/vocabulary', [VocabularyController::class, 'index']);
    Route::get('/vocabulary/stats', [VocabularyController::class, 'stats']);
    Route::get('/vocabulary-assets', [VocabularyAssetController::class, 'index']);
    Route::get('/vocabulary-assets/search-concept/{concept_id}', [VocabularyAssetController::class, 'searchByConcept']);
    Route::get('/vocabulary-assets/by-secret-id/{secret_id}', [VocabularyAssetController::class, 'findBySecretId']);
    Route::get('/audios', [AudioController::class, 'index']);
    Route::get('/proxy/skills/{skill_id}', [RevizyProxyController::class, 'skill'])->whereNumber('skill_id');
    Route::get('/proxy/units/{unit_id}', [RevizyProxyController::class, 'unit'])->whereNumber('unit_id');
    Route::get('/proxy/flashcard-categories/{category_id}', [RevizyProxyController::class, 'flashcardCategory'])->whereNumber('category_id');
    Route::get('/proxy/concepts/{concept_id}', [RevizyProxyController::class, 'concept'])->whereNumber('concept_id');

    Route::get('/generate-questions/{asset_id}', [QuestionGenerationController::class, 'generateForAsset'])
        ->whereNumber('asset_id');

    Route::get('/questions/counts', [QuestionController::class, 'counts']);
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/publish-attempts', [QuestionController::class, 'publishAttempts']);
    Route::post('/questions/check-duplicates', [QuestionController::class, 'checkDuplicates']);

    Route::get('/conjugaison', [ConjugaisonController::class, 'index']);
    Route::get('/grammaire', [GrammaireController::class, 'index']);
    Route::get('/roadmap', [RoadmapController::class, 'index']);

    Route::middleware('can:raiida-mutate')->group(function (): void {
        Route::post('/generate-audio-next', [AudioController::class, 'generateNext']);
        Route::post('/sync-assets', [VocabularyAssetController::class, 'syncAudioLinks']);
        Route::post('/vocabulary-assets/{asset_id}/upload-image', [ExternalAssetSyncController::class, 'uploadImageToRevizy'])
            ->whereNumber('asset_id');
        Route::post('/vocabulary-assets/{asset_id}/upload-audio', [ExternalAssetSyncController::class, 'uploadAudioToRevizy'])
            ->whereNumber('asset_id');
        Route::post('/vocabulary-assets/{asset_id}/upload-walidio', [ExternalAssetSyncController::class, 'uploadToWalidio'])
            ->whereNumber('asset_id');
        Route::post('/vocabulary-assets/{asset_id}/upload-flashcard', [FlashcardController::class, 'createFromVocabulary'])
            ->whereNumber('asset_id');
        Route::post('/vocabulary-assets/{asset_id}/create-concept', [ConceptController::class, 'createForVocabulary'])
            ->whereNumber('asset_id');
        Route::patch('/conjugaison/{id}/concept', [ConjugaisonController::class, 'updateConcept'])->whereNumber('id');
        Route::post('/concepts', [ConceptController::class, 'createGeneric']);
        Route::post('/concepts/recover-missing', [ConceptController::class, 'recoverMissing']);
        Route::post('/questions/{local_question_id}/publish', [QuestionPublishController::class, 'publish'])
            ->whereNumber('local_question_id');
        Route::post('/questions/{local_question_id}/unaccept', [QuestionPublishController::class, 'unaccept'])
            ->whereNumber('local_question_id');
        Route::delete('/questions/{attempt_id}', [QuestionController::class, 'destroy'])
            ->whereNumber('attempt_id');
    });

    Route::middleware(['can:raiida-admin', 'raiida.audit'])->group(function (): void {
        Route::post('/sync', [SyncController::class, 'start']);
        Route::post('/inspect', [IntegrityController::class, 'start']);
        Route::post('/extract-vocabulary', [VocabularyExtractionController::class, 'extractAll']);
        Route::post('/extract-vocabulary/{file_id}', [VocabularyExtractionController::class, 'extractOne']);
        Route::post('/vocabulary/classify-metadata', [VocabularyMetadataController::class, 'classify']);
        Route::post('/batch-generate-publish', [QuestionGenerationController::class, 'batchGeneratePublish']);
        Route::get('/api-providers', [ApiProviderController::class, 'index']);
        Route::post('/api-providers', [ApiProviderController::class, 'upsert']);
        Route::get('/api-providers/{slug}/usage', [ApiProviderController::class, 'usage']);
        Route::post('/api-providers/{slug}/refresh-usage', [ApiProviderController::class, 'refreshUsage']);
    });
});
