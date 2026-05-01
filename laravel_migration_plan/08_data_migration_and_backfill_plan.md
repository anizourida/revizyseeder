# 08. Data Migration and Backfill Plan

## 8.1 Data sources

Primary source for migration:

- SQLite file at repository root: `raiida.db`

Secondary sources (optional enrichment):

- `files/` directory tree
- `backend/static/vocab_assets/`
- `backend/static/audios/`
- external TAALIM JSON folders for conjugation/grammar rebuild scripts

## 8.2 Migration policy

- Initial import is snapshot-based.
- All import commands must be idempotent.
- Use stable business keys where possible.
- Preserve original IDs when safe to simplify parity checks.

## 8.3 Import sequence

1. `grades`
2. `subjects`
3. `periods`
4. `weeks`
5. `file_assets`
6. `vocabulary_items`
7. `audios`
8. `conjugaisons`
9. `grammaires`
10. `question_publish_attempts`
11. `questions` (legacy optional)

## 8.4 Idempotency keys per table

- `grades`: unique by `name`/`code`
- `subjects`: unique by (`grade`, `name`)
- `periods`: unique by (`subject`, `name`)
- `weeks`: unique by (`period`, `name`)
- `file_assets`: unique by (`week_id`, `filename`)
- `vocabulary_items`: unique by (`word`, `lesson_id`, `grade`)
- `audios`: unique by (`vocabulary_item_id`)
- `question_publish_attempts`: keep source `id` when possible

## 8.5 File/path reconciliation

During import, validate path references:

1. For each `vocabulary_items.image_path`:
- if starts `vocab_assets/`, confirm file exists in configured public disk.
- else treat as TAALIM external path.

2. For each `audio.file_path`/`vocabulary_items.audio_path`:
- confirm file exists under audios disk.

3. Generate a reconciliation report:
- missing images
- missing audio files
- orphan audios

## 8.6 Backfill tasks after import

1. Backfill derived/cache columns if introduced in Laravel.
2. Recompute stats caches (if implemented).
3. Optional: compute normalized hash for `question_data` to speed duplicate checks.
4. Validate that concept/media secret references remain intact.

## 8.7 Validation checklist

### Row count parity

- Compare source and target counts table-by-table.

### Integrity parity

- Random sample 50 `vocabulary_items`:
  - word
  - ar_translation
  - concept_id
  - media secret IDs

### Workflow parity

- Trigger sample endpoints after import:
  - `/stats`
  - `/vocabulary-assets?grade=N4&period=P1&week=SEM3`
  - `/questions/counts`

### External readiness

- Ensure no write endpoints run before env keys are configured.

## 8.8 Recommended artisan commands

Create dedicated commands:

- `php artisan raiida:import --source=/path/to/raiida.db`
- `php artisan raiida:verify-import`
- `php artisan raiida:reconcile-media`
- `php artisan raiida:backfill-derived`

## 8.9 Handling legacy `question` table

Option A (recommended now):

- Import as compatibility table.
- Keep read-only.

Option B (later):

- Migrate data into attempts/history model.
- Remove legacy endpoint usage.

## 8.10 Failure handling

- Wrap each table import in transaction chunking.
- Log failed rows with source primary key.
- Allow resume from table checkpoint.
- Never delete source data automatically.
