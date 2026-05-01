# 06. Laravel Target Architecture

## 6.1 Goals

1. Keep business behavior parity with current system.
2. Separate controllers from business workflows.
3. Make long operations queue-driven and observable.
4. Support safe multi-step migration with minimal downtime.

## 6.2 Proposed Laravel app structure

```text
app/
  Actions/
    Vocabulary/
    Questions/
    ExternalSync/
  Console/
    Commands/
  DTO/
  Enums/
  Http/
    Controllers/
      Api/
      Web/
    Requests/
    Resources/
  Jobs/
    Sync/
    Extraction/
    Audio/
    Publishing/
  Models/
  Policies/
  Repositories/
  Services/
    Content/
    Extraction/
    Questioning/
    External/
  Support/
    Media/
    Json/
    Logging/
config/
  services.php
  filesystems.php
  queue.php
database/
  migrations/
  seeders/
resources/
  views/
  js/
routes/
  api.php
  web.php
storage/
  app/public/vocab_assets
  app/public/audios
```

## 6.3 Layer responsibilities

### Controllers

- Accept/validate requests.
- Call orchestration services/actions.
- Return API resources.

### Services/Actions

- Core business rules and workflows.
- No direct HTTP request object usage.

### Repositories (optional but recommended here)

- Encapsulate query complexity for hierarchy/vocabulary/questions.

### Jobs

- Long-running tasks:
  - file sync
  - integrity inspection
  - vocabulary extraction
  - batch generate/publish
  - external media uploads

### DTOs + Resources

- Stabilize payload contracts.
- Keep backward-compatible fields while internal models evolve.

## 6.4 Module breakdown

1. `ContentCatalog` module
- hierarchy models and file assets
- sync + inspect workflows

2. `Vocabulary` module
- extraction and asset management
- lexical metadata

3. `Audio` module
- TTS integration and download/persist logic

4. `ExternalSync` module
- Revizy/Walidio clients
- flashcards and concepts

5. `QuestionEngine` module
- deterministic generation rules
- duplicate checking
- publish lifecycle

6. `PedagogyMetadata` module
- conjugaison/grammaire/roadmap endpoints

## 6.5 External integrations design

### Revizy

- Implement dedicated `RevizyClient` service.
- Centralize base URL/API key, retries, and error mapping.
- Wrap all endpoints:
  - files upload
  - skills/units/categories/concepts lookup
  - concepts create
  - flashcards create
  - questions publish

### Walidio

- Implement `WalidioClient` service.
- Enforce precondition checks (Revizy image secret exists).

### Local TTS service

- `AudioGenerationClient` with circuit breaker / retry policy.

## 6.6 Queue and scheduling strategy

- Queue driver: Redis recommended.
- Job classes:
  - `SyncFilesJob`
  - `InspectFilesJob`
  - `ExtractVocabularyJob`
  - `GenerateNextAudioJob`
  - `UploadVocabularyAssetJob`
  - `GenerateAndPublishQuestionsForAssetJob`
  - `BatchGeneratePublishJob`
- Scheduler:
  - optional nightly integrity checks
  - optional periodic sync jobs

## 6.7 Storage strategy

- Use Laravel filesystem disk `public` for assets.
- Mirror current paths for compatibility:
  - `storage/app/public/vocab_assets/...`
  - `storage/app/public/audios/...`
- Provide symlink via `php artisan storage:link`.
- For TAALIM external directory, configure custom disk path from env.

## 6.8 Security architecture

- Use Laravel Sanctum for API auth.
- Role-based gates/policies for high-risk endpoints.
- Move all API keys to `.env` and `config/services.php`.
- Encrypt sensitive tokens at rest if persisted.

## 6.9 Observability architecture

- Structured logs with context IDs (asset_id, concept_id, attempt_id).
- Failed job monitoring.
- Optional Telescope/Horizon for operations.
- Add domain events for major transitions:
  - file downloaded
  - vocab extracted
  - media synced
  - question published/failed

## 6.10 Frontend strategy

Short term:

- Keep existing static UI behavior by preserving API contracts.

Mid term:

- Move to Blade + modular JS or Inertia/React under Laravel.
- Decommission standalone jQuery monolith gradually by module.
