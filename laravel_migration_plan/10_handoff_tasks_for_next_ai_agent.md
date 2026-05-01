# 10. Handoff Tasks for the Next AI Agent

Use this as the execution board when starting the Laravel build.

## 10.1 Sprint 1 - Foundation

### Task 1

- Create Laravel project skeleton and configure environments.
- Add DB connection and queue config.

Acceptance:

- `php artisan migrate` runs cleanly.
- app boots and returns health response.

### Task 2

- Implement core migrations/models:
  - grades, subjects, periods, weeks, file_assets
  - vocabulary_items, audios
  - conjugaisons, grammaires
  - question_publish_attempts

Acceptance:

- all tables exist with required indexes and FKs.

### Task 3

- Implement import command from source SQLite.

Acceptance:

- imported row counts match baseline report.

## 10.2 Sprint 2 - Read APIs

### Task 4

- Implement read endpoints with parity payloads:
  - `/stats`, `/files`, `/tree`
  - `/vocabulary`, `/vocabulary/stats`, `/vocabulary-assets`
  - `/audios`, `/questions`, `/questions/counts`
  - `/api/conjugaison`, `/api/grammaire`, `/api/roadmap`

Acceptance:

- static UI pages can render list views without frontend code changes.

## 10.3 Sprint 3 - Write workflows

### Task 5

- Implement sync and inspection jobs + endpoints.

### Task 6

- Implement vocabulary extraction service and endpoints.

### Task 7

- Implement asset sync endpoints (`upload-image`, `upload-audio`, `upload-walidio`) with external clients.

Acceptance:

- one full asset row can be synced end-to-end through UI.

## 10.4 Sprint 4 - Questions engine

### Task 8

- Port deterministic question generator to PHP.

### Task 9

- Implement duplicate check and publish/unaccept endpoints.

### Task 10

- Implement batch generate/publish endpoint using queues.

Acceptance:

- generated questions visually preview and publish with expected statuses.

## 10.5 Sprint 5 - Hardening

### Task 11

- Add auth and role permissions for mutation routes.

### Task 12

- Add test coverage and observability.

### Task 13

- Run cutover dry run and parity check.

Acceptance:

- no critical parity gap remains.
- failed-job and error dashboards available.

## 10.6 Copy-paste prompts for implementation agent

### Prompt A - bootstrap

"Create a new Laravel app for Raiida, implement migrations/models from `04_database_migration_blueprint.md`, and generate import command `raiida:import` from SQLite source while preserving legacy IDs when possible."

### Prompt B - read parity

"Implement read endpoints in `05_api_contract_and_endpoint_mapping.md` with exact response compatibility for current static UI, including vocabulary alias fields (`image`, `audio`, `name`, `name_ar`)."

### Prompt C - external sync

"Implement Revizy and Walidio client services with retries/timeouts and wire routes for image/audio/walidio upload from vocabulary assets. Use env-based credentials only."

### Prompt D - question engine

"Port deterministic rules from `03_algorithms_and_business_rules.md` to PHP and implement `/generate-questions/{asset_id}`, duplicate checking, publish/unaccept, and batch generate/publish with queue jobs."

### Prompt E - security and quality

"Add Sanctum auth, role-based authorization for write routes, feature tests for critical APIs, and structured logging with workflow context IDs."

## 10.7 Final go-live checklist

1. Credentials rotated and not hardcoded.
2. Queue workers running and monitored.
3. DB backups tested.
4. Full operator workflow smoke-tested.
5. Rollback plan validated.
