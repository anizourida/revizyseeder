# Appendix D - Current FastAPI Endpoint Catalog

Source: `backend/main.py`

## Core and files

- `GET /`
- `POST /sync`
- `POST /inspect`
- `GET /files`
- `GET /stats`
- `GET /tree`

## Vocabulary

- `POST /extract-vocabulary`
- `POST /extract-vocabulary/{file_id}`
- `GET /vocabulary`
- `GET /vocabulary/stats`
- `GET /vocabulary-assets`
- `POST /sync-assets`
- `GET /vocabulary-assets/search-concept/{concept_id}`
- `GET /vocabulary-assets/by-secret-id/{secret_id}`

## Audio

- `GET /audios`
- `POST /generate-audio-next`

## Asset external sync

- `POST /vocabulary-assets/{asset_id}/upload-image`
- `POST /vocabulary-assets/{asset_id}/upload-audio`
- `POST /vocabulary-assets/{asset_id}/upload-walidio`

## Proxy helpers

- `GET /proxy/skills/{skill_id}`
- `GET /proxy/units/{unit_id}`
- `GET /proxy/flashcard-categories/{category_id}`
- `GET /proxy/concepts/{concept_id}`

## Flashcards and concepts

- `POST /vocabulary-assets/{asset_id}/upload-flashcard`
- `POST /vocabulary-assets/{asset_id}/create-concept`
- `POST /api/concepts`

## Questions

- `GET /generate-questions/{asset_id}`
- `POST /batch-generate-publish`
- `GET /questions/counts`
- `GET /questions`
- `DELETE /questions/{attempt_id}`
- `POST /questions/check-duplicates`
- `POST /questions/{local_question_id}/publish`
- `POST /questions/{local_question_id}/unaccept`
- `GET /questions/publish-attempts`

## Pedagogy metadata

- `GET /api/conjugaison`
- `GET /api/grammaire`
- `GET /api/roadmap`

## HTML views

- `GET /roadmap.html`
- `GET /grammaire.html`
- `GET /conjugaison.html`
