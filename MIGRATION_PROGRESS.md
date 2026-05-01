# Raiida Laravel Migration Progress

Last updated: 2026-02-26

## Context and Decisions

- Canonical source DB (local copy): `/Users/macbook/Rida/ProductionRepoRevizy/Seeder/database/source/raiida.db`
- Target app path: `/Users/macbook/Rida/ProductionRepoRevizy/Seeder`
- DB target (phase 1/2): SQLite
- API style: `/api/*` prefix
- Security stance: secure now (Sanctum auth on read endpoints in this phase)
- Queue connection: `database`
- Legacy IDs: preserved during import where possible (implemented)

## Phase 1 - Bootstrap and Foundation

### Completed

- Offline Laravel app bootstrap in this folder.
- Fresh migration set created for:
  - framework tables (`users`, `password_reset_tokens`, `failed_jobs`, `jobs`, `job_batches`, `cache`, `sessions`, `personal_access_tokens`)
  - Raiida domain tables (`grades`, `subjects`, `periods`, `weeks`, `file_assets`, `vocabulary_items`, `audios`, `conjugaisons`, `grammaires`, `question_publish_attempts`)
- Raiida domain models created under `App\\Models\\Raiida` with relations/casts.
- Config/env externalization added:
  - `config/raiida.php`
  - `.env` and `.env.example` updated with source path and integration keys placeholders.
- API routes replaced with secure Raiida read parity routes in `routes/api.php`.

### Verification

- `php artisan migrate:fresh --force` -> PASS
- `php artisan route:list --path=api` -> PASS

## Phase 2 - Data Import and Read Parity (requested subset)

### Completed

- Import command implemented: `php artisan raiida:import {--source=...}`
- Imported source tables with idempotent upsert and legacy ID preservation:
  - `grade -> grades`
  - `subject -> subjects`
  - `period -> periods`
  - `week -> weeks`
  - `fileasset -> file_assets`
  - `vocabularyitem -> vocabulary_items`
  - `audio -> audios`
  - `conjugaison -> conjugaisons`
  - `grammaire -> grammaires`
  - `questionpublishattempt -> question_publish_attempts`
- Read endpoints implemented with parity-first payload behavior:
  - `GET /api/stats`
  - `GET /api/files`
  - `GET /api/tree`
  - `GET /api/vocabulary`
  - `GET /api/vocabulary/stats`
  - `GET /api/vocabulary-assets` (includes aliases: `image`, `audio`, `name`, `name_ar`)
  - `GET /api/audios`
  - `GET /api/conjugaison`
  - `GET /api/grammaire`
  - `GET /api/roadmap`
  - `GET /api/questions`
  - `GET /api/questions/counts`
- `questions/counts` supports:
  - default all-status counts (parity)
  - `?status=published` published-only
  - `?view=both` returns both maps

### Verification

- `php artisan raiida:import` -> PASS
- `php artisan test tests/Feature/Raiida` -> PASS (14 tests, 107 assertions)
- `php artisan test` -> PASS (21 tests, 120 assertions)

## Current Phase Status

- Phase 1 bootstrap: COMPLETE
- Phase 2 requested scope (import + read parity subset): COMPLETE
- Auth bootstrap (`/api/auth/*` token flow): COMPLETE
- Phase 3 write workflow baseline (`/sync`, `/inspect`, `/extract-vocabulary`, `/sync-assets`): COMPLETE
- Phase 4 question engine migration: COMPLETE
- Phase 6 web UI question studio baseline: COMPLETE
- Phase 7 project isolation into clean Raiida app folder: COMPLETE

## Auth Bootstrap (Secure Access)

### Completed

- Added Sanctum token auth endpoints:
  - `POST /api/auth/login`
  - `POST /api/auth/logout`
  - `GET /api/auth/me`
- Added login throttling on auth endpoint.
- Added operator bootstrap command:
  - `php artisan raiida:operator:create --name=... --email=... --password=...`
- Verified command execution with local operator creation.

### Verification

- `php artisan route:list --path=api/auth` -> PASS
- `php artisan test tests/Feature/Raiida/RaiidaAuthTest.php` -> PASS (4 tests, 20 assertions)
- `php artisan test` -> PASS (27 tests, 141 assertions)

## Phase 3 - Write Workflow Baseline

### Completed

- Added queued workflow endpoints:
  - `POST /api/sync` dispatches `SyncFilesJob`
  - `POST /api/inspect` dispatches `InspectFilesJob`
  - `POST /api/extract-vocabulary` dispatches `ExtractVocabularyJob`
- Added parity-compatible single extraction endpoint:
  - `POST /api/extract-vocabulary/{file_id}`
- Added asset audio-link sync endpoint:
  - `POST /api/sync-assets`
- Implemented service layer:
  - `SyncFilesService`
  - `FileInspectionService`
  - `VocabularyExtractionService`
  - `AssetSyncService`
- Added env/config for sync source, files root, and extraction marker phrase.
- Added workflow dispatch and contract tests (`RaiidaWorkflowDispatchTest`).

### Verification

- `php artisan route:list --path=api` -> PASS
- `php artisan test tests/Feature/Raiida/RaiidaWorkflowDispatchTest.php` -> PASS (6 tests, 21 assertions)
- `php artisan test` -> PASS (27 tests, 141 assertions)

## Phase 4 - Question Engine Migration

### Completed

- Ported deterministic question-generation service from Python:
  - `QuestionGeneratorService` (distractor tiers + question families + post-processing)
  - numeric extraction parity fix for `P0/SEM0` handling
- Added question studio services:
  - `QuestionStudioService` (generate/check duplicates/publish/unaccept/batch)
  - `QuestionJsonNormalizer` (sorted-key normalization for duplicate matching)
  - `RevizyQuestionApiClient` (env/config driven external publish client)
- Added queue-ready batch job:
  - `BatchGeneratePublishJob`
- Added API controllers and routes for full question flow parity:
  - `GET /api/generate-questions/{asset_id}`
  - `POST /api/batch-generate-publish`
  - `POST /api/questions/check-duplicates`
  - `POST /api/questions/{local_question_id}/publish`
  - `POST /api/questions/{local_question_id}/unaccept`
  - `GET /api/questions/publish-attempts`
  - `DELETE /api/questions/{attempt_id}`
- Extended question attempts controller:
  - delete endpoint
  - optional status-filtered publish attempts
  - duplicate check endpoint
- Added feature tests for core question-engine flows:
  - `tests/Feature/Raiida/RaiidaQuestionEngineTest.php`

### Verification

- `php artisan test tests/Feature/Raiida/RaiidaQuestionEngineTest.php` -> PASS (8 tests, 53 assertions)
- `php artisan route:list --path=api` (question routes) -> PASS
- `php artisan test` -> PASS (35 tests, 194 assertions)

## Phase 5 - Frontend Compatibility (Incremental)

### Completed (this step)

- Added question UI compatibility endpoints:
  - `GET /api/vocabulary-assets/search-concept/{concept_id}`
  - `GET /api/vocabulary-assets/by-secret-id/{secret_id}`
- Implemented response payload parity for:
  - concept search result shape with nested `vocabulary` object
  - secret-id lookup fallback (image secret first, then audio secret)
- Implemented parity `detail` error messages for missing resources:
  - concept not found
  - secret id not found
- Added/updated feature coverage in `RaiidaMetadataEndpointsTest`:
  - auth protection for both endpoints
  - success payload shape assertions
  - 404 parity assertions

### Verification

- `php artisan route:list --path=api` (vocabulary-assets compatibility routes) -> PASS
- `php artisan test tests/Feature/Raiida/RaiidaMetadataEndpointsTest.php` -> PASS (7 tests, 60 assertions)
- `php artisan test` -> PASS (37 tests, 218 assertions)

### Browser-Flow Smoke Check (API-driven)

- Added end-to-end smoke test for question studio flow:
  - `tests/Feature/Raiida/RaiidaQuestionStudioSmokeTest.php`
- Covered full operator path:
  - generate questions
  - search concept
  - media lookup by secret id
  - duplicate check before publish
  - publish question
  - duplicate check after publish
  - list publish attempts
  - unaccept question
  - list/count questions
  - delete attempt

### Verification

- `php artisan test tests/Feature/Raiida/RaiidaQuestionStudioSmokeTest.php` -> PASS (1 test, 31 assertions)
- `php artisan test` -> PASS (38 tests, 249 assertions)

## Security Hardening - RBAC Baseline

### Completed

- Added role model to users:
  - new migration: `add_role_to_users_table` (`role` + index, default `operator`)
  - `User` model constants and helpers:
    - `ROLE_ADMIN`, `ROLE_OPERATOR`, `ROLE_REVIEWER`
    - `canRaiidaMutate()`, `isRaiidaAdmin()`
- Added authorization gates in `AuthServiceProvider`:
  - `raiida-mutate`
  - `raiida-admin` (reserved for stricter admin-only actions)
- Applied role-based middleware to Raiida mutation routes:
  - `/sync`, `/inspect`, `/extract-vocabulary*`, `/sync-assets`
  - `/batch-generate-publish`
  - `/questions/{local_question_id}/publish`
  - `/questions/{local_question_id}/unaccept`
  - `DELETE /questions/{attempt_id}`
- Updated auth payloads to include role:
  - `POST /api/auth/login`
  - `GET /api/auth/me`
- Updated operator creation command to set explicit role and support `--role=` for bootstrap.
- Added dedicated RBAC feature coverage:
  - reviewer forbidden on mutation endpoints (`403`)
  - operator/admin allowed on mutation endpoints

### Verification

- `php artisan migrate --force` -> PASS (`add_role_to_users_table`)
- `php artisan test tests/Feature/Raiida/RaiidaAuthorizationTest.php` -> PASS (3 tests)
- `php artisan test` -> PASS (41 tests, 272 assertions)

## Security Hardening - Admin Separation (Selected High-Risk)

### Completed

- Tightened high-risk mutation endpoints to `raiida-admin`:
  - `POST /sync`
  - `POST /inspect`
  - `POST /extract-vocabulary`
  - `POST /extract-vocabulary/{file_id}`
  - `POST /batch-generate-publish`
- Kept day-to-day operator workflows under `raiida-mutate`:
  - `POST /sync-assets`
  - question publish/unaccept/delete attempt
- Updated authorization tests to reflect operator/admin split.

### Verification

- `php artisan route:list --path=api --json` -> PASS (middleware shows `Authorize:raiida-admin` on selected endpoints)
- `php artisan test tests/Feature/Raiida/RaiidaAuthorizationTest.php tests/Feature/Raiida/RaiidaWorkflowDispatchTest.php tests/Feature/Raiida/RaiidaQuestionEngineTest.php` -> PASS
- `php artisan test` -> PASS (41 tests, 277 assertions)

## Operations - Reuse Existing Downloaded Files

### Completed

- Added local file hydration command:
  - `php artisan revizyseeder:hydrate-files --source=/Users/macbook/Rida/fichiers-raiida/files --mode=copy`
- Command capabilities:
  - copies/links files from legacy local `files` folder into current `RAIIDA_FILES_ROOT`
  - supports `--mode=copy|hardlink|symlink`
  - updates `file_assets` download state and size based on actual local file presence
  - supports `--dry-run`, `--skip-transfer`, `--skip-db`, `--limit`
- Added test coverage:
  - `tests/Feature/Raiida/RevizySeederHydrateFilesCommandTest.php`

### Verification

- `php artisan test tests/Feature/Raiida/RevizySeederHydrateFilesCommandTest.php` -> PASS

## Files Resource Data Scope Simplification

### Completed

- Scoped Files admin dataset to:
  - subject `FR` only
  - grades `N1` to `N6` only
  - periods `1` to `5` only
  - weeks `1` to `6` only
- Removed subject filter from Files inline and dropdown filters.
- Switched grade/period/week filters to code-based selection:
  - `grade_code`
  - `period_code`
  - `week_code`
- Aligned Files modal summary counts with the same scoped dataset.

### Verification

- `php artisan test tests/Feature/Raiida/RaiidaFilamentDashboardTest.php` -> PASS (8 tests, 25 assertions)
- `php artisan test` -> PASS (57 tests, 413 assertions)

## Security Hardening - Mutation Audit Logging

### Completed

- Added dedicated admin-mutation audit middleware:
  - `RaiidaAdminAuditLog` (logs start/completed/failed with actor + endpoint + timing)
  - resolves/propagates `workflow_context_id` from `X-Workflow-Context-Id` header or generated UUID
  - injects `X-Workflow-Context-Id` in response for traceability
- Registered middleware alias:
  - `raiida.audit`
- Applied middleware to admin-only mutation routes:
  - `/sync`, `/inspect`, `/extract-vocabulary*`, `/batch-generate-publish`
- Added workflow context propagation from request to queued jobs:
  - `SyncFilesJob`, `InspectFilesJob`, `ExtractVocabularyJob`
  - includes initiator metadata (`user_id`, `email`, `role`) in job logs
- Updated controllers to dispatch jobs with context + initiator metadata.
- Added audit-log assertion coverage:
  - `RaiidaAuthorizationTest::test_admin_mutation_routes_emit_audit_logs_with_context_id`

### Verification

- `php artisan route:list --path=api --json` -> PASS (admin routes include `RaiidaAdminAuditLog`)
- `php artisan test tests/Feature/Raiida/RaiidaAuthorizationTest.php tests/Feature/Raiida/RaiidaWorkflowDispatchTest.php` -> PASS
- `php artisan test` -> PASS (42 tests, 282 assertions)

## Phase 6 - Web UI Question Studio (Baseline)

### Completed

- Added web controller and route for question studio:
  - `GET /raiida/question-studio`
- Added Blade UI page:
  - `resources/views/raiida/question-studio.blade.php`
- Implemented UI parity wiring for core studio actions:
  - auth login/logout/session restore
  - paginated vocabulary-assets list with filters
  - deterministic question generation per asset
  - duplicate check + secret-id media lookup
  - publish/unaccept question actions
  - admin batch generate+publish action
  - paginated publish-attempts list + delete action
- Added consistent pager UI styling across assets and attempts.
- Added route/page feature test:
  - `tests/Feature/Raiida/RaiidaQuestionStudioPageTest.php`

### Verification

- `php artisan test tests/Feature/Raiida/RaiidaQuestionStudioPageTest.php` -> PASS (1 test, 4 assertions)
- `php artisan test` -> PASS (43 tests, 286 assertions)

## Phase 7 - Project Isolation Into Clean Raiida App

### Completed

- Created isolated app workspace at:
  - `/Users/macbook/Rida/ProductionRepoRevizy/Seeder`
- Removed non-Raiida application modules and routes from the copied baseline:
  - legacy API controllers/modules (`Api/V1`, `Api/System`, `Api/Admin`)
  - legacy admin/UI modules (`app/Admin`, `app/Filament`, related views/assets)
  - legacy utility/services/jobs/models/factories/seeders not required by Raiida migration scope
- Removed legacy migration archive folder:
  - `database/migrations_legacy`
- Updated provider bootstrapping to remove deleted module references:
  - `AppServiceProvider`, `config/app.php`
- Updated environment paths to point to the isolated folder database/assets.
- Disabled package auto-discovery for non-Raiida runtime layers:
  - Filament, Livewire, Telescope

### Verification

- `php artisan route:list` -> PASS (Raiida API/UI routes only; no `superadmin/*` or `telescope/*` routes)
- `php artisan test` -> PASS (38 tests, 275 assertions)

## Phase 8 - Full Frontend Migration + External Workflow Parity

### Completed

- Replaced the previous single-page question studio baseline with full static-UI parity migration:
  - source-ported assets:
    - `public/raiida-ui/css/style.css`
    - `public/raiida-ui/js/app.js`
    - `public/raiida-ui/js/common.js` (auth token + `/api` rewrite layer)
  - source-ported Blade pages:
    - `resources/views/raiida/app.blade.php`
    - `resources/views/raiida/grammaire.blade.php`
    - `resources/views/raiida/roadmap.blade.php`
    - shared sidebar: `resources/views/raiida/partials/sidebar.blade.php`
- Added route-based frontend navigation (not demo/one-page only):
  - `/raiida/{module}` with module routes for all required UI modules.
  - compatibility redirects:
    - `/raiida/question-studio`
    - `/roadmap.html`
    - `/grammaire.html`
    - `/conjugaison.html`
- Added web UI controller:
  - `App\\Http\\Controllers\\Web\\RaiidaUiController`
- Added missing write/proxy parity API endpoints and services:
  - `POST /api/generate-audio-next`
  - `POST /api/vocabulary-assets/{asset_id}/upload-image`
  - `POST /api/vocabulary-assets/{asset_id}/upload-audio`
  - `POST /api/vocabulary-assets/{asset_id}/upload-walidio`
  - `GET /api/proxy/skills/{id}`
  - `GET /api/proxy/units/{id}`
  - `GET /api/proxy/flashcard-categories/{id}`
  - `GET /api/proxy/concepts/{id}`
  - `POST /api/vocabulary-assets/{asset_id}/upload-flashcard`
  - `POST /api/vocabulary-assets/{asset_id}/create-concept`
  - `POST /api/concepts`
- Added env/config support updates:
  - `RAIIDA_SOURCE_STATIC_PATH`
  - `RAIIDA_AUDIO_GENERATOR_ENABLED`
- Added feature coverage for external sync/proxy/flashcard/concept flows:
  - `tests/Feature/Raiida/RaiidaExternalSyncEndpointsTest.php`
- Updated frontend page test to validate new routed UI:
  - `tests/Feature/Raiida/RaiidaQuestionStudioPageTest.php`

### Verification

- `php artisan route:list --path=raiida` -> PASS
- `php artisan route:list --path=api` -> PASS (new parity endpoints present)
- `php artisan test tests/Feature/Raiida` -> PASS (39 tests, 306 assertions)

## Phase 9 - Filament Admin Dashboard

### Completed

- Re-enabled Filament and Livewire package discovery in `composer.json`.
- Installed Filament panel scaffolding (`/admin`) and registered panel provider.
- Updated Filament panel branding/composition in:
  - `app/Providers/Filament/AdminPanelProvider.php`
- Restricted Filament panel access to Raiida mutate roles via `User` model:
  - `canAccessFilament()` and `canAccessPanel()` rely on `canRaiidaMutate()`.

## Phase 10 - Filament Reset (Simple Starter + FilesResource)

### Completed

- Switched Filament panel branding to `RevizySeeder`.
- Simplified admin panel to starter mode:
  - default Filament dashboard only
  - resources-driven navigation
  - removed page discovery from panel registration
- Added first resource `FilesResource`:
  - path: `/admin/files`
  - model: `App\Models\Raiida\FileAsset`
  - table columns: filename, hierarchy (grade/subject/period/week), size, downloaded/corrupt states, timestamp
  - filters: downloaded, corrupt, session
  - actions:
    - header action: `Fetch` (dispatches `SyncFilesJob`)
    - row action: `Download` (download selected file via service)
    - bulk action: `Download Selected`
- Added real-time download tracking for files:
  - new `file_assets` fields: `download_state`, `download_progress`, `download_error`, `download_started_at`, `download_finished_at`
  - sync service now updates progress while downloading in background queue jobs
  - Filament files table now shows live status + progress bar with `2s` polling
  - fixed default ordering: `id desc`; non-ordering columns are not user-sortable
- Added explicit unavailable-source status badge in Files table:
  - `Status: Not Available (404)` for permanent source 404 download failures
  - keeps existing `Corrupt`, `Downloading`, `Downloaded`, `Failed`, `Pending` badges
- Enhanced downloading status with live per-file MB indicator:
  - `Status: Downloading (X.XX MB)` reads current partial file size from disk
  - updates automatically with existing table polling (`2s`)
- Added combined filtering UX (inline + filter icon):
  - inline filters remain available for `Grade`, `Subject`, `Period`, `Week`
  - filter icon dropdown is enabled and includes non-inline `Corrupt` filter
- Extended `SyncFilesService` with single-record download support:
  - `downloadExistingAsset(FileAsset $asset): string`
  - fallback path reconstruction for legacy rows without `local_path`
- Removed runtime dependency on old project absolute paths:
  - local source DB copy: `database/source/raiida.db`
  - local files root: `files/`
  - local source static root: `storage/source-static`
  - updated `.env`, `.env.example`, and deployment docs accordingly
- Updated import test to use `config('raiida.source_sqlite_path')` instead of hardcoded old path.
- Updated Filament test coverage for starter layout and resource route.
- Added Filament regression test for 404 badge visibility on `/admin/files`.
- Added Filament regression test for live downloading MB status label.
- Files page still verified after filter UX update (`/admin/files` load + status tests).

### Verification

- `php artisan route:list --path=admin` -> PASS
- `php artisan test tests/Feature/Raiida/RaiidaFilamentDashboardTest.php` -> PASS (5 tests, 10 assertions)

## Phase 11 - Fetch Pipeline Rebuild (Metadata + Download Reliability)

### Completed

- Rebuilt `SyncFilesService::run()` with robust fetch pipeline behavior:
  - stale `downloading` recovery before sync execution
  - metadata normalization + deterministic deduplication prior to persistence
  - cache lock to prevent concurrent sync runs
  - richer run summary (`raw_total`, `total`, `duplicates`, `processed`, `downloaded`, `skipped`, `pending`, `failed`, `locked`)
- Added metadata dedupe/selection logic:
  - dedupe key: `(matiere, niveau, periode, semaine, filename)`
  - conflict resolution: prefer newest `updatedAt/createdAt` item
- Fixed filename/extension handling for real API payloads:
  - preserve original `.ppt` filenames (do not force invalid fallback `.pptx`)
  - valid extension set now includes `pptx`, `ppsx`, and `ppt`
- Added stale-state recovery for interrupted downloads:
  - if file exists on disk, mark as downloaded
  - otherwise reset to `pending` with recovery message
- Added sync lock configuration to env/config:
  - `REVIZYSEEDER_FETCH_LOCK_KEY`
  - `REVIZYSEEDER_FETCH_LOCK_SECONDS`
- Hardened HTTP download behavior:
  - explicit `connectTimeout(15)`
  - retries on transient failures
- Improved queue timeout safety:
  - `config/queue.php` now uses `QUEUE_RETRY_AFTER` (default `7200`) instead of fixed low retry window
- Refactored fetch orchestration to metadata-first + per-file queued downloads:
  - `SyncFilesJob` now runs metadata sync only (`runMetadataOnly`) and then queues one job per pending file
  - new `DownloadFileAssetJob` handles each file download independently (`tries=3`, lock-protected per file)
  - batched dispatch via `Bus::batch(...)` with configurable batch naming
  - improved retry granularity and failure isolation at file level
- Updated Filament fetch monitor to include per-file queue/batch activity:
  - fetch guard now blocks duplicate launches while workflow is active
  - queued jobs count now includes both sync and per-file download jobs
  - summary uses active download batch metadata from `job_batches`
- Added env/config for batch and file-level locks:
  - `REVIZYSEEDER_FETCH_BATCH_NAME`
  - `REVIZYSEEDER_FILE_LOCK_SECONDS`
- Added dedicated feature tests:
  - metadata dedupe + `.ppt` filename preservation
  - stale downloading recovery for existing disk file
  - sync orchestration creates download batch jobs after metadata sync
  - per-file download job marks file as downloaded correctly
- Fixed production queue failure in batched downloads:
  - added `Batchable` trait to `DownloadFileAssetJob` to support Laravel batch internals (`withBatchId`)
  - retried previously failed `SyncFilesJob` entries after fix
- Reduced permanent download failure retry storms:
  - `DownloadFileAssetJob` now treats permanent HTTP client failures (`4xx`, except `408/429`) as terminal file failures without throwing job exceptions
  - `collectDownloadCandidateIds()` now excludes permanently failed assets from automatic requeue
  - added regression test for HTTP `404` behavior
- Fixed interrupted-download resume flow after worker restart:
  - stale `downloading` rows are now recovered to `pending` (not incorrectly marked downloaded) when local file is only partial
  - `downloadExistingAsset()` and sync inline path now restart interrupted partial files instead of skipping
  - kept parity behavior for newly discovered metadata rows that already exist on disk (first sync marks them downloaded)
  - added regression tests for partial-file recovery and restart behavior
- Fixed queue worker memory exhaustion during large fetch runs:
  - `dispatchDownloadBatch()` now dispatches empty batch then adds jobs in chunks (memory-safe)
  - added configurable `REVIZYSEEDER_FETCH_BATCH_CHUNK_SIZE` (default `300`)
  - `collectDownloadCandidateIds()` now streams records with `lazyById()` instead of loading all candidates at once

### Verification

- `php -l app/Services/Raiida/SyncFilesService.php` -> PASS
- `php -l tests/Feature/Raiida/RaiidaSyncFilesServiceTest.php` -> PASS
- `php -l app/Jobs/Raiida/SyncFilesJob.php` -> PASS
- `php -l app/Jobs/Raiida/DownloadFileAssetJob.php` -> PASS
- `php -l app/Filament/Resources/FilesResource.php` -> PASS
- `php -l tests/Feature/Raiida/RaiidaSyncFilesPipelineTest.php` -> PASS
- `php artisan config:clear` -> PASS
- `php artisan test tests/Feature/Raiida/RaiidaSyncFilesServiceTest.php` -> PASS (2 tests, 19 assertions)
- `php artisan test tests/Feature/Raiida/RaiidaSyncFilesPipelineTest.php tests/Feature/Raiida/RaiidaSyncFilesServiceTest.php` -> PASS (5 tests, 32 assertions)
- `php artisan test tests/Feature/Raiida/RaiidaSyncFilesServiceTest.php tests/Feature/Raiida/RaiidaSyncFilesPipelineTest.php tests/Feature/Raiida/RaiidaWorkflowDispatchTest.php tests/Feature/Raiida/RaiidaFilamentDashboardTest.php` -> PASS (13 tests, 56 assertions)
- `php artisan test` -> PASS (52 tests, 373 assertions)
- `php artisan queue:failed` -> PASS (no failed jobs after retry)

## Phase 12 - PPT Presentation Data Extraction Pipeline

### Completed

- Added dedicated Python extraction script in project:
  - `scripts/extract_lesson_data.py`
  - converts `.ppsx` to `.pptx` compatibility format
  - extracts text/image/video elements per slide
  - writes lesson output as:
    - `storage/app/presentation_data/{lesson_id}/data.json`
    - `storage/app/presentation_data/{lesson_id}/assets/*`
- Added DB state tracking for extraction in `file_assets`:
  - `is_presentation_data_extracted`
  - `presentation_slide_count`
  - `presentation_json_path`
  - `presentation_assets_dir`
  - `presentation_extraction_error`
  - `presentation_extracted_at`
- Added Laravel service wrapper:
  - `App\Services\Raiida\PresentationDataExtractionService`
  - executes extractor script, parses output JSON, and updates DB state
- Added queue job for extraction:
  - `App\Jobs\Raiida\ExtractPresentationDataJob`
- Added artisan command:
  - `php artisan revizyseeder:extract-presentation-data`
  - supports inline extraction or queue dispatch (`--queue`)
  - supports targeting specific file assets (`--id=*`) and force mode (`--force`)
- Wired auto-extraction after successful file download:
  - `DownloadFileAssetJob` now dispatches `ExtractPresentationDataJob` (configurable)
- Added config/env controls:
  - `RAIIDA_PRESENTATION_PYTHON_BIN`
  - `RAIIDA_PRESENTATION_SCRIPT_PATH`
  - `RAIIDA_PRESENTATION_OUTPUT_ROOT`
  - `RAIIDA_PRESENTATION_PROCESS_TIMEOUT`
  - `RAIIDA_PRESENTATION_QUEUE`
  - `RAIIDA_PRESENTATION_FILE_LOCK_SECONDS`
  - `RAIIDA_PRESENTATION_AUTO_EXTRACT_AFTER_DOWNLOAD`
- Added feature tests:
  - `tests/Feature/Raiida/RevizySeederExtractPresentationDataCommandTest.php`
  - covers inline extraction success
  - covers queue dispatch mode
  - covers post-download auto-dispatch of extraction job
- Added PPT-like extracted-data preview in admin:
  - `GET /admin/files/preview/{fileAsset}` renders extracted slides (text/image/video)
  - `GET /admin/files/preview/{fileAsset}/asset/{assetPath}` securely serves extracted media
  - new `Preview` row action in Filament `FilesResource` menu
  - element placement uses normalized percentages from source `bbox` EMU values
- Extended extractor metadata for layout fidelity:
  - `slide_width_emu`
  - `slide_height_emu`
  - preview falls back to standard 16:9 EMU when missing
- Hardened extraction failure handling to avoid queue failure storms:
  - `ExtractPresentationDataJob` now treats permanent file issues as non-retry terminal outcomes:
    - `Package not found`
    - `Bad CRC-32`
    - `unrecognized shape type`
  - these errors remain stored in `file_assets.presentation_extraction_error`, but no longer create repeated failed queue jobs
  - `DownloadFileAssetJob` auto-dispatch now excludes legacy `.ppt` files (extractor targets `.pptx/.ppsx`)
  - command `revizyseeder:extract-presentation-data` now scopes to `.pptx/.ppsx` only
- Hardened python extractor recursion to skip malformed/unsupported shapes instead of aborting the whole file.

### Verification

- `php artisan migrate --force` -> PASS (`add_presentation_extraction_columns_to_file_assets_table`)
- `php artisan test tests/Feature/Raiida/RevizySeederExtractPresentationDataCommandTest.php` -> PASS (3 tests, 14 assertions)
- `php artisan test tests/Feature/Raiida/RaiidaSyncFilesPipelineTest.php` -> PASS (3 tests, 13 assertions)
- `php artisan test tests/Feature/Raiida/RaiidaFilamentDashboardTest.php` -> PASS (10 tests, 33 assertions)
- `php artisan test tests/Feature/Raiida/RevizySeederExtractPresentationDataCommandTest.php` -> PASS (5 tests, 18 assertions)
- `php artisan test tests/Feature/Raiida/RaiidaSyncFilesPipelineTest.php` -> PASS (3 tests, 13 assertions)
- `php artisan test` -> PASS (64 tests, 439 assertions)
- `php artisan revizyseeder:extract-presentation-data --id=1` -> PASS (real extraction completed; JSON+assets generated)

## Phase 13 - Conjugaison Resource + Deterministic Extraction

### Completed

- Added seeded reference tables for conjugaison scope:
  - `conjugaison_grades` (`N1..N6`)
  - `conjugaison_periods` (`P1..P5`)
  - `conjugaison_weeks` (`SEM1..SEM4`)
- Extended `conjugaisons` table to store extraction metadata:
  - FK refs (`grade_id`, `period_id`, `week_id`)
  - `name`, `related_raw_data`
  - source tracking (`source_file_asset_id`, `source_lesson_id`, `source_slide_id`)
  - scoring/meta (`confidence_score`, `extraction_meta`)
- Added deterministic conjugaison extraction pipeline:
  - `ConjugaisonTextAnalyzer` for exact rule-based pattern matching/scoring (no AI generation)
  - `ConjugaisonExtractionService` to scan extracted slide JSON and persist best candidate per `N/P/SEM`
  - improved lesson-id parsing for combined grade sources (`N5&6`, `N1&2`) by splitting into per-grade scopes
  - added question-prompt extraction (exercise/assessment lines) linked to each conjugaison objective
  - added full scope coverage generation for `N1..N6`, `P1..P5`, `SEM1..SEM6` with deterministic prefill (`180` rows)
  - rows with no detected conjugaison keep empty lesson cells (blank `name`/`question`) while preserving visible scope indicators (`N/P/SEM`)
  - queued job: `ExtractConjugaisonLessonsJob`
  - artisan command: `revizyseeder:extract-conjugaison` (`--queue`, `--force`)
- Added DB scope uniqueness on conjugaison matrix:
  - new migration `add_unique_scope_index_to_conjugaisons_table`
  - deduplicates legacy rows and enforces one record per (`n`, `p`, `sem`)
- Added Filament `ConjugaisonResource`:
  - columns for `id`, `n`, `p`, `sem`, grade/period/week refs, `name`, `verbe`, `tense`, score, source metadata
  - added `question` column extracted from slide content
  - grade/period/week displayed as numeric values (1..6, 1..5, 1..4)
  - empty fields rendered as `Not found`
  - filters for grade/period/week
  - admin header action to queue extraction
- Added API payload extension on `/api/conjugaison` to expose new metadata while keeping parity fields.
- Added/updated tests:
  - `ConjugaisonTextAnalyzerTest`
  - `ConjugaisonExtractionPipelineTest`
  - Filament dashboard resource access coverage
- Fixed analyzer false-positive bug for phrases like:
  - `Conjugaison : Le verbe au présent continu.`

### Verification

- `php artisan migrate --force` -> PASS
- `php artisan db:seed --class=ConjugaisonReferenceSeeder --force` -> PASS
- `php artisan test tests/Feature/Raiida/ConjugaisonExtractionPipelineTest.php` -> PASS
- `php artisan test tests/Unit/Raiida/ConjugaisonTextAnalyzerTest.php tests/Feature/Raiida/RaiidaFilamentDashboardTest.php` -> PASS
- `php artisan test` -> PASS (78 tests, 502 assertions)

## Next Tasks

1. Add persisted sync-run/audit table to track one fetch execution (`started_at`, `finished_at`, totals, failure counts, throughput, batch id).
2. Add normalization map for subject/grade anomalies from source API (`Math/Maths/MATHS`, outlier `niveau`) with explicit fallback policy.
3. Build next Filament resources in the same style (`VocabularyResource`, `AssetsResource`, then `GrammaireResource`/`RoadmapResource`).
