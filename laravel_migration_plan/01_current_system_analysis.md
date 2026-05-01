# 01. Current System Analysis

## 1.1 High-level architecture

Current stack is a monolithic content-operations app:

- Backend API/server: FastAPI (`backend/main.py`)
- Data access: SQLModel over SQLite (`backend/models.py`, `backend/database.py`)
- Heavy business workflows in services:
  - `backend/services/downloader.py`
  - `backend/services/inspector.py`
  - `backend/services/vocabulary_extractor.py`
  - `backend/services/audio_generator.py`
  - `backend/services/asset_manager.py`
- Question generation engine:
  - deterministic rules in `backend/question_generator.py`
- Frontend actually used by backend root route:
  - static/jQuery app `backend/static/index.html` + `backend/static/js/app.js`
- Extra frontend scaffold (partial/legacy):
  - React app in `frontend/src` (API contracts do not match current backend)

## 1.2 Core product purpose

This app manages educational content operations for French learning assets:

- Syncs PPT/PPS files from an external source API.
- Stores hierarchical metadata (grade -> subject -> period -> week -> file).
- Validates file integrity.
- Extracts vocabulary from lesson slides.
- Generates/links audio and media assets.
- Syncs media to external systems (Revizy/Walidio).
- Creates flashcards/concepts in external platform.
- Generates and publishes pedagogical questions with deterministic logic.
- Provides grammar/conjugation/roadmap dashboards.

## 1.3 Runtime behavior and orchestration

Main orchestration is API-triggered and mostly synchronous loops:

- Background tasks for sync/inspect/extract (FastAPI `BackgroundTasks`).
- Long loops in request handlers for batch generate/publish.
- External APIs called directly inside controllers and service functions.
- Local file system acts as major data source/sink for media and PPT assets.

## 1.4 Data baseline snapshot (root `raiida.db`)

### Table counts

- `grade`: 9
- `subject`: 40
- `period`: 97
- `week`: 456
- `fileasset`: 2328
- `vocabularyitem`: 823
- `audio`: 823
- `questionpublishattempt`: 6699
- `conjugaison`: 39
- `grammaire`: 71
- `question` (legacy): 2

### Vocabulary completeness

- with concept_id: 762 / 823
- with revizy image secret: 823 / 823
- with revizy audio secret: 823 / 823
- with walidio id: 823 / 823
- with flashcard id: 704 / 823
- with Arabic translation: 752 / 823
- with lexical_type: 823 / 823
- with gender: 541 / 823
- with distractor_group: 823 / 823

### File pipeline state

- downloaded: 2325 / 2328
- integrity checked: 2150 / 2328
- corrupt: 16 / 2328
- vocab extracted flag: 147 / 2328
- files tagged as session `S1`: 435

### Question publish attempts

- published: 6683
- failed: 10
- unaccepted: 6

## 1.5 Technical debt and migration-relevant risks

### Critical

1. Secrets in source code
- Hardcoded Revizy, Walidio, Dropbox tokens, and x-app-key exist in repository files.

2. Mixed DB path usage
- `database.py` uses relative `sqlite:///raiida.db`, while repository contains both root `raiida.db` and `backend/raiida.db`.
- Risk of writing/reading different databases depending on process CWD.

3. Controller/service coupling
- `backend/main.py` contains business orchestration + external HTTP + file IO logic.
- Hard to test and maintain.

### High

4. No authentication/authorization on admin endpoints
- Current APIs are effectively open admin operations.

5. Non-idempotent external sync endpoints
- Some operations can be retried with partial side effects.

6. Long-running operations in web process
- Batch generation and uploads can exceed normal request lifecycle expectations.

### Medium

7. Contract drift between UIs
- Static jQuery UI is aligned with backend.
- React frontend uses some endpoints that do not exist (example: `POST /questions`), indicating stale scaffold.

8. Partial schema legacy
- `question` table remains though current flow relies on `questionpublishattempt`.

## 1.6 What should be preserved exactly during migration

- Deterministic question-generation logic behavior.
- Vocabulary extraction heuristics from PPT/PPS slides.
- Existing concept/flashcard/publish workflows with external systems.
- Existing IDs/relations where possible to keep historical references valid.
- Existing static asset paths compatibility (`vocab_assets`, `audios`, and TAALIM data mounts).

## 1.7 What should be changed during migration

- Move all secrets to environment and rotate credentials.
- Move orchestration to Laravel services/jobs/queues.
- Add auth + permission boundaries.
- Normalize and enforce DB constraints.
- Add observability, retries, and idempotency keys.
- Replace large jQuery monolith with Blade + modular JS or SPA (phased).
