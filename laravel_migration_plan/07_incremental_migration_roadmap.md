# 07. Incremental Migration Roadmap

## Phase 0 - Preparation and freeze controls

### Objectives

- Ensure migration starts from stable source snapshot.
- Remove unknowns that can corrupt parity checks.

### Tasks

1. Freeze schema-changing operations in Python app during migration window.
2. Pick canonical source DB file (`/raiida.db`) and archive checksum.
3. Export key baseline metrics (counts and completeness).
4. Rotate leaked credentials and create fresh env-based secrets.
5. Define route parity acceptance list.

### Exit criteria

- Baseline report approved.
- All credentials externalized for new Laravel app.

## Phase 1 - Laravel bootstrap and domain foundation

### Objectives

- Build empty but structured Laravel skeleton.

### Tasks

1. Initialize Laravel project and environments.
2. Create migrations and Eloquent models for all domain tables.
3. Seed minimal reference data (grades/subjects, if needed).
4. Add API route stubs with placeholder responses.
5. Add auth scaffolding (Sanctum + basic roles).

### Exit criteria

- `php artisan migrate` succeeds.
- All target routes exist with contract placeholders.

## Phase 2 - Data import and read parity

### Objectives

- Bring real data into Laravel DB and match read endpoints.

### Tasks

1. Build import command for hierarchy + files + vocabulary + audio + pedagogy metadata + attempts.
2. Verify row counts vs baseline.
3. Implement read endpoints:
- `/stats`, `/files`, `/tree`
- `/vocabulary`, `/vocabulary/stats`, `/vocabulary-assets`
- `/audios`, `/questions`, `/questions/counts`
- `/api/conjugaison`, `/api/grammaire`, `/api/roadmap`
4. Preserve response field aliases required by static UI.

### Exit criteria

- Read endpoints match expected payloads in manual smoke tests.

## Phase 3 - Write workflows and external integrations

### Objectives

- Recreate mutation and sync operations.

### Tasks

1. Implement file sync and integrity jobs.
2. Implement vocabulary extraction service and endpoints.
3. Implement Revizy/Walidio client services.
4. Implement upload endpoints:
- image/audio/walidio
- flashcard create
- concept create
5. Implement audio generation next-item workflow.

### Exit criteria

- End-to-end asset sync from UI works in Laravel.

## Phase 4 - Question engine migration

### Objectives

- Port deterministic question generation and publishing pipeline.

### Tasks

1. Port `question_generator.py` rules exactly to PHP service classes.
2. Implement duplicate check endpoint using normalized JSON comparisons.
3. Implement per-question publish/unaccept endpoints.
4. Implement batch generate/publish orchestration (prefer queue chain/batch).
5. Validate generated question shape against existing examples.

### Exit criteria

- Single-asset generation and batch publish both run with expected results.

## Phase 5 - Frontend compatibility and UI migration

### Objectives

- Keep operations usable during backend switch.

### Tasks

1. Point existing static UI to Laravel routes and run full smoke test.
2. Fix any payload mismatches.
3. Optionally move pages to Blade/inertia module-by-module:
- dashboard/files
- vocab/assets/audio
- flashcards/concepts
- questions studio

### Exit criteria

- Operators can perform all critical workflows in Laravel.

## Phase 6 - Hardening and production cutover

### Objectives

- Ensure reliability, security, and rollback safety.

### Tasks

1. Add automated test suite and CI checks.
2. Add queue workers, Horizon (optional), and alerting.
3. Add structured logs and failure dashboards.
4. Run parallel shadow period (Python and Laravel read compare).
5. Cut traffic to Laravel and monitor.

### Exit criteria

- Stable production behavior for agreed burn-in period.

## Rollback strategy

1. Keep Python app deployable during cutover.
2. Snapshot Laravel DB before each high-risk phase.
3. Use feature flags for external write operations.
4. If parity issue found, disable writes in Laravel and revert traffic.

## Suggested implementation order for fastest value

1. Read parity endpoints
2. External media upload flows
3. Question engine
4. Batch/pipeline automation
