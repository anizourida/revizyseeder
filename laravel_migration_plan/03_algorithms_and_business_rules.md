# 03. Algorithms and Business Rules

## 3.1 File sync algorithm (`downloader.run_sync`)

### Inputs

- Remote metadata list from content API.
- Base directory for file storage.

### Steps

1. Fetch all metadata items.
2. For each item:
- Resolve hierarchy entities (`grade`, `subject`, `period`, `week`) with get-or-create semantics.
- Build deterministic relative path.
- Parse session ID from filename pattern `_(S[1-6])`.
- Upsert `fileasset` row (by filename + week).
- If file exists on disk, mark downloaded/size and skip.
- Else download file and update DB flags.

### Key rules

- File considered valid candidate only if extension is pptx/ppsx (fallback naming applied otherwise).
- Sync currently sequential (chosen for SQLite write safety).

## 3.2 Integrity check algorithm (`inspector.run_inspection`)

1. Load files where `is_downloaded=1 AND is_integrity_checked=0`.
2. For each file:
- If missing on disk: mark `is_downloaded=0`.
- Else validate by extension type:
  - OOXML presentations with python-pptx
  - OLE presentations with `olefile`
- Set `is_integrity_checked=1`.
- Set `is_corrupt` inverse of validation result.

## 3.3 Vocabulary extraction algorithm (`vocabulary_extractor.process_lesson_file`)

### Candidate selection

- Triggered for S1 files mainly (in global extraction).
- Skips combined grades (`N1&2`) in strict mode.

### Slide parsing

1. Convert PPSX -> PPTX when needed.
2. Iterate slides recursively through shape tree.
3. Collect text blocks and image candidates.
4. Compute image score by placeholder hints + area.

### Golden-slide rule

- Slide must contain marker phrase: `"qui veut répéter"` (normalized) to be eligible.

### Word selection rule

- Remove noise text (`objectifs`, `enseignant`, etc.).
- First filtered text becomes vocabulary `word`.

### Image selection rule

- Sort images by `(score DESC, area DESC)`.
- Pick top image.
- Hash-based dedup of binary blobs prevents duplicate file writes.

### Persistence rule

- Insert new `vocabularyitem` only if no same `(word, lesson_id, grade)` exists.
- Update matching `fileasset` rows: `is_vocab_extracted`, `vocab_count`.

## 3.4 Distractor selection algorithm (`question_generator.select_distractors`)

For target vocabulary item, chooses up to N distractors using 8-tier fallback:

1. Same week + same distractor_group + lexical compatible
2. Previous weeks + same group + lexical compatible
3. Same week + same group + different lexical type
4. Previous weeks + same group + different lexical type
5. Same week + compatible type + different group
6. Previous weeks + compatible type + different group
7. Same week + any
8. Any from same grade

Additional rules:

- Candidate must have both image and audio secrets.
- Period/week are ordered chronologically for recency ranking.
- Compatible lexical groups are predefined (for example `interjection/locution/phrase`).

## 3.5 Question construction algorithm (`question_generator.generate_questions`)

### Built question families

- universal text -> image
- universal image -> text
- universal audio -> image
- universal image -> audio
- universal text -> image+audio card
- universal image+audio -> text
- universal audio -> text+image card
- grammar trap (noun article confusion)
- fill text
- letter by letter

### Grammar trap constraints

- Only lexical type `nom` with known gender.
- Skip plural article forms.
- Apply grade-based difficulty gates.

### Fill text constraints

- Skip accented terms for typing practicality.
- Accept multiple correct forms (original, bare noun, possible definite form).

### Post-process

- Capitalize French articles in names/answers.
- Cap output to max 10 questions.

## 3.6 Duplicate detection algorithm (`/questions/check-duplicates` + publish endpoint)

1. Group incoming questions by concept_id.
2. Fetch all published attempts for these concept IDs.
3. Normalize/compare JSON payloads using sorted keys.
4. Flag duplicates and reuse existing remote question ID in publish flow.

## 3.7 Batch generate/publish algorithm (`/batch-generate-publish`)

1. Find concepts already published in `questionpublishattempt`.
2. Select vocabulary with concept_id not in published set.
3. Skip items missing required fields (`revizy_image_file_id`, `lexical_type`).
4. Build distractor pool from same grade.
5. Generate questions.
6. For each generated question:
- store pending attempt
- publish to Revizy
- mark attempt published/failed with metadata
7. Return aggregate report (`generated`, `published`, `failed`, `skipped`).

## 3.8 Auto-sync UI workflow algorithm (assets)

In jQuery UI:

1. Iterate rows of filtered assets table.
2. Optional skip logic by grade.
3. If image not synced -> upload image.
4. If audio not synced -> upload audio.
5. If Walidio pending and image available -> upload Walidio.
6. Supports stop-request toggle.

## 3.9 Business invariants to preserve in Laravel

1. VocabularyItem uniqueness by `(word, lesson_id, grade)` semantics.
2. Question generation must stay deterministic and rule-based.
3. Duplicate detection compares normalized question JSON payloads.
4. External media secret IDs are system-of-record references for publish flows.
5. Conjugation/grammar roadmap aggregation keyed by `(n,p,sem)`.
