# 05. API Contract and Endpoint Mapping

## 5.1 Migration principle

Keep existing API contracts first for safe frontend continuity, then introduce versioned cleanup.

- Phase 1: Laravel mirrors existing FastAPI routes.
- Phase 2: Add internal service layer and typed request/response DTOs.
- Phase 3: Introduce `/api/v2` normalized contracts.

## 5.2 Route mapping (FastAPI -> Laravel)

### System/file operations

- `POST /sync`
  - Laravel: `SyncController@start`
  - Action: dispatch `SyncFilesJob`

- `POST /inspect`
  - Laravel: `IntegrityController@start`
  - Action: dispatch `InspectFilesJob`

- `GET /stats`
  - Laravel: `DashboardController@stats`

- `GET /files`
  - Laravel: `FileAssetController@index`

- `GET /tree`
  - Laravel: `FileAssetController@tree`

### Vocabulary operations

- `POST /extract-vocabulary`
  - Laravel: `VocabularyExtractionController@extractAll`

- `POST /extract-vocabulary/{file_id}`
  - Laravel: `VocabularyExtractionController@extractOne`

- `GET /vocabulary`
  - Laravel: `VocabularyController@index`

- `GET /vocabulary/stats`
  - Laravel: `VocabularyController@stats`

- `GET /vocabulary-assets`
  - Laravel: `VocabularyAssetController@index`
  - Note: preserve legacy response aliases

- `POST /sync-assets`
  - Laravel: `VocabularyAssetController@syncAudioLinks`

- `GET /vocabulary-assets/search-concept/{concept_id}`
  - Laravel: `VocabularyAssetController@searchByConcept`

- `GET /vocabulary-assets/by-secret-id/{secret_id}`
  - Laravel: `VocabularyAssetController@findBySecretId`

### Audio operations

- `GET /audios`
  - Laravel: `AudioController@index`

- `POST /generate-audio-next`
  - Laravel: `AudioController@generateNext`

### External media uploads

- `POST /vocabulary-assets/{asset_id}/upload-image`
  - Laravel: `ExternalAssetSyncController@uploadImageToRevizy`

- `POST /vocabulary-assets/{asset_id}/upload-audio`
  - Laravel: `ExternalAssetSyncController@uploadAudioToRevizy`

- `POST /vocabulary-assets/{asset_id}/upload-walidio`
  - Laravel: `ExternalAssetSyncController@uploadToWalidio`

### Proxy/lookup endpoints

- `GET /proxy/skills/{skill_id}`
  - Laravel: `RevizyProxyController@skill`

- `GET /proxy/units/{unit_id}`
  - Laravel: `RevizyProxyController@unit`

- `GET /proxy/flashcard-categories/{category_id}`
  - Laravel: `RevizyProxyController@flashcardCategory`

- `GET /proxy/concepts/{concept_id}`
  - Laravel: `RevizyProxyController@concept`

### Flashcards/concepts

- `POST /vocabulary-assets/{asset_id}/upload-flashcard?flashcard_category_id=...`
  - Laravel: `FlashcardController@createFromVocabulary`

- `POST /vocabulary-assets/{asset_id}/create-concept`
  - Laravel: `ConceptController@createForVocabulary`

- `POST /api/concepts`
  - Laravel: `ConceptController@createGeneric`

### Questions studio

- `GET /generate-questions/{asset_id}`
  - Laravel: `QuestionGenerationController@generateForAsset`

- `POST /batch-generate-publish`
  - Laravel: `QuestionGenerationController@batchGeneratePublish`
  - Action: queue-based orchestration recommended

- `GET /questions/counts`
  - Laravel: `QuestionAttemptController@counts`

- `GET /questions`
  - Laravel: `QuestionAttemptController@index`

- `DELETE /questions/{attempt_id}`
  - Laravel: `QuestionAttemptController@destroy`

- `POST /questions/check-duplicates`
  - Laravel: `QuestionAttemptController@checkDuplicates`

- `POST /questions/{local_question_id}/publish`
  - Laravel: `QuestionPublishController@publish`

- `POST /questions/{local_question_id}/unaccept`
  - Laravel: `QuestionPublishController@unaccept`

- `GET /questions/publish-attempts`
  - Laravel: `QuestionAttemptController@publishAttempts`

### Grammar/conjugation/roadmap

- `GET /api/conjugaison`
  - Laravel: `ConjugaisonController@index`

- `GET /api/grammaire`
  - Laravel: `GrammaireController@index`

- `GET /api/roadmap`
  - Laravel: `RoadmapController@index`

### HTML routes (temporary compatibility)

- `GET /`, `/roadmap.html`, `/grammaire.html`, `/conjugaison.html`
  - Laravel: `Web\PageController` actions or static view routes

## 5.3 Request/response compatibility notes

1. Preserve field aliases in vocabulary assets:
- `image`, `audio`, `name`, `name_ar`

2. Preserve status payload shapes for UI loops:
- audio generation (`success/retry/error/complete`)
- batch generation summary object

3. Preserve error format (HTTP + `detail`) initially for frontend safety.

## 5.4 API hardening to add in Laravel

- Authentication for all mutation routes.
- Authorization by role/permission (admin, operator, reviewer).
- Rate limiting for expensive endpoints.
- Idempotency keys for external publish endpoints.
- Input validation via Form Requests.

## 5.5 Suggested Laravel route groups

- `routes/api.php`
  - `Route::middleware(['auth:sanctum'])->group(...)` for writes
  - read routes may remain public in early local phase if needed
- `routes/web.php`
  - static page routes and dashboard views
