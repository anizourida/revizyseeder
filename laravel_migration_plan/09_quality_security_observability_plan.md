# 09. Quality, Security, and Observability Plan

## 9.1 Testing strategy

## Unit tests

- question generation rule units (all question families)
- distractor tier selection
- extraction text normalization and marker filtering
- duplicate comparison logic
- image resize helper behavior

## Feature/API tests

- route contract tests for all migrated endpoints
- validation error format tests
- publish/unaccept lifecycle tests
- batch endpoint summary structure tests

## Integration tests

- Revizy/Walidio clients with mock servers
- audio generation client with mock TTS API
- filesystem integration for vocab/audio paths

## Regression packs

- fixed golden input -> expected question JSON outputs
- sample PPT extraction expected vocabulary outputs

## 9.2 Security hardening checklist

1. Move all tokens/keys to env (`.env`, secrets manager).
2. Rotate previously exposed credentials.
3. Add auth for mutation endpoints.
4. Add role-based access controls for admin operations.
5. Add CSRF/session protection for web forms.
6. Add rate limits for:
- batch generate/publish
- external upload endpoints
- proxy endpoints
7. Sanitize and validate all uploaded file paths and IDs.
8. Add audit logs for critical actions:
- publish question
- create concept/flashcard
- upload external media

## 9.3 Reliability patterns

- queue retries with backoff for external APIs
- dead-letter/failed job handling
- idempotency checks for publish/create endpoints
- timeout + circuit breaker policy for external HTTP clients
- job-level correlation IDs

## 9.4 Logging and metrics

## Structured logs

Include context fields:

- `asset_id`
- `concept_id`
- `attempt_id`
- `external_request_id`
- `workflow` name

## Metrics to track

- sync duration and items processed
- extraction success/failure counts
- audio generation success/retry/error rates
- Revizy/Walidio API error rates
- question publish success/failure rates
- queue backlog and failed jobs

## Dashboards/alerts

- alert on spike in failed publish attempts
- alert on failed jobs > threshold
- alert on external API timeout increase

## 9.5 Performance considerations

1. Replace heavy in-memory list filtering with SQL where possible.
2. Add DB indexes for frequent filters.
3. Paginate large result sets consistently.
4. Move batch operations to queues.
5. Consider JSON hash column for fast duplicate lookup.

## 9.6 CI/CD quality gates

- `phpstan`/`larastan` static analysis
- `phpunit` full test suite
- coding standards (`pint`)
- migration dry-run checks
- optional API smoke tests post-deploy

## 9.7 Definition of done for each migrated feature

1. API contract parity validated.
2. Unit + feature tests added.
3. Logs include workflow context.
4. Access control applied.
5. Failure modes handled and documented.
