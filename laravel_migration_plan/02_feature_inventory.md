# 02. Full Feature Inventory

## 2.1 File ingestion and cataloging

### Metadata sync

- Trigger file sync from remote content API.
- Build/maintain hierarchy:
  - Grade
  - Subject
  - Period
  - Week
  - FileAsset
- Extract session from filename (`S1..S6`).
- Persist file metadata and download status.

### Physical file download

- Download PPT/PPS assets into structured folders:
  - `files/{subject}/niveau_{n}/periode_{p}/semaine_{sem}/{filename}`
- Skip existing files.
- Update file size and flags in DB.

### Integrity inspection

- Validate `.pptx/.ppsx` by opening with python-pptx.
- Validate `.ppt/.pps` via OLE signature.
- Mark missing files as not downloaded.
- Mark invalid files as corrupt.

## 2.2 File and stats dashboards

### Dashboard metrics

- Total files
- Downloaded files
- Corrupt files
- Total downloaded size
- Completion percentage

### File explorer

- Tree navigation (grade -> subject -> period -> week -> file).
- File metadata display with status badges.

### Files table

- Filters by grade/subject/period/week/status.
- Search by filename.
- Sort by multiple columns.
- Optional grouping (grade/subject/week).
- Column visibility preferences persisted locally.

## 2.3 Vocabulary extraction and management

### Extraction pipeline

- Bulk extraction endpoint (background).
- Single-file extraction endpoint.
- Filtered extraction mainly for FR, N1..N6, session S1, downloaded and not already extracted.

### Vocabulary listing

- Paginated vocabulary retrieval by grade/period/week.
- Stats endpoint by grade.
- Grid and table UI modes.
- Client-side keyword search.

### Vocabulary asset listing

- Paginated vocabulary asset endpoint with backward-compat fields (`image`, `audio`, `name`, `name_ar`).
- Includes Revizy/Walidio/concept/flashcard IDs and lexical metadata.

## 2.4 Audio generation workflow

### Sequential generation

- Find next vocabulary item without audio.
- Call local TTS API (`localhost:3000/api/generate`).
- Download generated wav file.
- Store `audio` row and link to vocabulary.
- Return status (`success`, `retry`, `error`, `complete`) with cooldown hints.

### Audio dashboard

- List generated audios with metadata.
- Start/stop loop from UI.
- Countdown-based retry/wait handling.

## 2.5 External media sync workflows

### Revizy file upload

- Upload image file to Revizy files API.
- Upload audio file to Revizy files API.
- Auto-resize oversized images before upload.
- Save returned secret IDs to vocabulary item.

### Walidio upload

- Upload image to Walidio with metadata.
- Requires Revizy image secret as prerequisite.
- Save returned Walidio image ID.

### Asset sync utility

- Backfill `vocabularyitem.audio_path` from `audio` table.

### Auto-sync mode in UI

- Iterates visible asset rows.
- Attempts image, audio, then Walidio upload.
- Skip certain grades (N4 in current JS logic).
- Stop/resume behavior.

## 2.6 Flashcards workflows

### Category verification

- Checks category details via Revizy proxy endpoint.
- Validates that category is leaf (no children) before upload.

### Flashcard creation

- Bulk upload pending items to selected category.
- Applies front text styling tags:
  - `[BLUE]Le|Un[/BLUE]`
  - `[PINK]La|Une[/PINK]`
- Uses image/audio secret IDs.
- Persists returned flashcard ID.

## 2.7 Concept creation workflows

### Vocabulary concept creator

- Verify skill and unit via Revizy proxy endpoints.
- Bulk create concepts per filtered vocabulary slice.
- Persist concept ID + skill/unit IDs into vocabulary.

### Generic concept creation

- Endpoint to create concept outside vocabulary assets.
- Used by conjugation/grammar tooling.

## 2.8 Conjugation and grammar tooling

### Conjugation data API/UI

- Filter by `n/p/sem`.
- Shows verb, tense, raw data, concept metadata.
- Raw-data copy helper in UI.

### Grammar data API/UI

- Filter/search grammar entries by level and objective text.

### Roadmap API/UI

- Merges vocabulary counts + conjugation + grammar by `(n,p,sem)`.
- Displays pedagogical roadmap table.

## 2.9 Question generation and publishing studio

### Deterministic generation

- Generate multiple question formats for one vocabulary item.
- Requires concept_id + revizy media IDs + lexical type.

### Question preview and enrichment

- Parses JSON question payloads.
- Resolves media secret IDs to local assets for preview.
- Duplicate check against previously published local records.

### Publish/unaccept lifecycle

- Publish single question to Revizy API.
- Mark question as unaccepted.
- Local attempt records include status and remote ID.

### Batch generation and publish

- Process vocabulary items with concept IDs but no published questions.
- Generate and publish per item.
- Record per-item result summary and failures.

### Questions audit tools

- List question attempts with statuses.
- Delete local attempt entries.
- Export CSV of concept/media secrets for filtered vocab.

## 2.10 Supporting scripts (outside main API)

- Dropbox archival script.
- Google Drive/Sheet archival script.
- Local downloader script with logging.
- Data extractors for conjugation and grammar from external JSON trees.
- Schema/data migration helper scripts.
- Integrity check utilities.

## 2.11 Legacy/partial features

- React frontend exists but is not the primary production UI path.
- `question` table exists but current workflow uses `questionpublishattempt`.
