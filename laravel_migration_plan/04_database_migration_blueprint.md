# 04. Database Migration Blueprint (SQLite -> Laravel)

## 4.1 Current key tables

- `grade`
- `subject` -> FK `grade_id`
- `period` -> FK `subject_id`
- `week` -> FK `period_id`
- `fileasset` -> FK `week_id`
- `vocabularyitem`
- `audio` -> FK `vocabulary_id` (unique)
- `questionpublishattempt`
- `conjugaison`
- `grammaire`
- `question` (legacy)

## 4.2 Current pain points

1. No strict unique constraints for hierarchy combinations.
2. Legacy table (`question`) overlaps with newer question attempt flow.
3. Missing explicit audit columns for many workflows.
4. Soft delete support absent for admin operations.
5. Potential DB ambiguity due duplicated physical db files (`/raiida.db` and `/backend/raiida.db`).

## 4.3 Target Laravel database design

Use MySQL or PostgreSQL for production (recommended), SQLite only for local/test.

### Hierarchy domain

1. `grades`
- `id`, `code` (N1..N6), `name`, timestamps
- unique: `code`

2. `subjects`
- `id`, `grade_id`, `code`, `name`, timestamps
- unique: (`grade_id`, `code`)

3. `periods`
- `id`, `subject_id`, `code` (P1..), `name`, timestamps
- unique: (`subject_id`, `code`)

4. `weeks`
- `id`, `period_id`, `code` (SEM1..), `name`, timestamps
- unique: (`period_id`, `code`)

### Content files

5. `file_assets`
- `id`, `week_id`, `filename`, `local_path`, `original_url`, `size_bytes`
- status flags: `is_downloaded`, `is_integrity_checked`, `is_corrupt`, `is_vocab_extracted`
- `session_id`, `vocab_count`
- timestamps: `downloaded_at`, `created_at`, `updated_at`
- unique (recommended): (`week_id`, `filename`)

### Vocabulary domain

6. `vocabulary_items`
- `id`, `word`, `image_path`, `audio_path`
- taxonomy: `grade`, `subject`, `period`, `week`, `lesson_id`
- language fields: `ar_translation`
- NLP fields: `lexical_type`, `gender`, `distractor_group`, `distractor_subgroup`
- external refs: `revizy_image_file_id`, `revizy_audio_file_id`, `walidio_image_id`, `flashcard_id`, `concept_id`, `revizy_skill_id`, `revizy_unite_id`
- timestamps
- unique (recommended): (`word`, `lesson_id`, `grade`)

7. `audios`
- `id`, `vocabulary_item_id`, `text`, `file_path`, timestamps
- unique: `vocabulary_item_id`

### Question workflows

8. `question_publish_attempts`
- `id`, `local_question_id`, `concept_id`, `name`, `question_data` JSON
- `status` enum-like (`pending`, `published`, `unaccepted`, `failed`)
- `revizy_question_id`, `error_message`
- timestamps: `published_at`, `unaccepted_at`, `failed_at`, standard timestamps

9. `questions` (optional final model)
- Can remain as compatibility table or be removed after full migration if unused.

### Pedagogical metadata

10. `conjugaisons`
- `id`, `n`, `p`, `sem`, `verbe`, `tense`, `raw_data`
- optional external refs: `concept_id`, `week`, `revizy_skill_id`, `revizy_unite_id`
- timestamps

11. `grammaires`
- `id`, `n`, `p`, `sem`, `objectif`, `lesson_title`, `raw_data`, timestamps

### System/ops tables

12. `job_batches`, `failed_jobs`, `jobs`, `cache`, `sessions` (Laravel standard)
13. `users`, `roles`, `permissions`, pivot tables (new for admin auth)

## 4.4 Eloquent model mapping

- `Grade` -> `grades`
- `Subject` -> `subjects`
- `Period` -> `periods`
- `Week` -> `weeks`
- `FileAsset` -> `file_assets`
- `VocabularyItem` -> `vocabulary_items`
- `Audio` -> `audios`
- `QuestionPublishAttempt` -> `question_publish_attempts`
- `Conjugaison` -> `conjugaisons`
- `Grammaire` -> `grammaires`

## 4.5 Migration ordering (Laravel)

1. core auth + users/roles
2. hierarchy tables (`grades/subjects/periods/weeks`)
3. file assets
4. vocabulary + audios
5. conjugaisons + grammaires
6. question publish attempts
7. compatibility tables/views (if needed)
8. secondary indexes and constraints tuning

## 4.6 Required indexes

### High priority

- `vocabulary_items`: (`grade`, `period`, `week`)
- `vocabulary_items`: (`concept_id`)
- `vocabulary_items`: (`revizy_image_file_id`), (`revizy_audio_file_id`)
- `file_assets`: (`is_downloaded`, `is_integrity_checked`, `is_vocab_extracted`, `session_id`)
- `question_publish_attempts`: (`concept_id`, `status`)

### Nice-to-have

- full-text index for vocabulary `word` (db-dependent)
- expression index for normalized `question_data` hash (if computing hash column)

## 4.7 Data normalization improvements

1. Convert free-form `grade/period/week` string columns in `vocabulary_items` into optional FK links over time.
2. Keep existing string fields initially for zero-risk compatibility.
3. Add computed/cache columns only after parity validation.

## 4.8 Compatibility strategy

Short term:

- Preserve API response field names currently used by static UI (`image`, `audio`, `name`, `name_ar`).
- Keep old-like endpoint payload shape until frontend rewrite is complete.

Medium term:

- Introduce `/api/v2/*` normalized contracts.
- Deprecate compatibility adapters.
