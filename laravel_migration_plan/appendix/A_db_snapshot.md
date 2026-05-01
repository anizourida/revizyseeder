# Appendix A - Database Snapshot Used for Planning

Snapshot source: repository root `raiida.db`.

## A.1 Table counts

| Table | Count |
|---|---:|
| grade | 9 |
| subject | 40 |
| period | 97 |
| week | 456 |
| fileasset | 2328 |
| vocabularyitem | 823 |
| audio | 823 |
| questionpublishattempt | 6699 |
| conjugaison | 39 |
| grammaire | 71 |
| question (legacy) | 2 |

## A.2 Vocabulary completeness

| Metric | Count |
|---|---:|
| with concept_id | 762 |
| with revizy_image_file_id | 823 |
| with revizy_audio_file_id | 823 |
| with walidio_image_id | 823 |
| with flashcard_id | 704 |
| with ar_translation | 752 |
| with lexical_type | 823 |
| with gender | 541 |
| with distractor_group | 823 |
| with audio_path | 823 |

## A.3 Question attempt statuses

| Status | Count |
|---|---:|
| published | 6683 |
| failed | 10 |
| unaccepted | 6 |

## A.4 File processing statuses

| Metric | Count |
|---|---:|
| downloaded | 2325 |
| integrity_checked | 2150 |
| corrupt | 16 |
| vocab_extracted | 147 |
| session_s1 | 435 |

## A.5 Lexical distributions (vocabularyitem)

### lexical_type

- nom: 541
- verbe: 111
- adjectif: 95
- locution: 61
- phrase: 10
- interjection: 3
- pronom: 2

### gender

- feminine: 222
- masculine: 319
- empty/null: 282
