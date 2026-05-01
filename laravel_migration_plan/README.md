# Raiida -> Laravel Migration Planning Pack

This folder is a complete handoff package to rebuild the current Python/FastAPI project as a structured Laravel application.

## What this pack contains

1. `01_current_system_analysis.md`
   Current architecture, runtime behavior, and technical debt snapshot.
2. `02_feature_inventory.md`
   Full feature inventory from backend APIs + static frontend workflows.
3. `03_algorithms_and_business_rules.md`
   Core extraction, selection, and publishing logic translated into deterministic rules.
4. `04_database_migration_blueprint.md`
   Current SQLite schema, target Laravel schema, constraints, and migration approach.
5. `05_api_contract_and_endpoint_mapping.md`
   Endpoint-by-endpoint FastAPI -> Laravel route/controller mapping.
6. `06_laravel_target_architecture.md`
   Proposed Laravel module boundaries, folder layout, services/jobs, storage, and queue design.
7. `07_incremental_migration_roadmap.md`
   Phased execution plan from bootstrap to production cutover.
8. `08_data_migration_and_backfill_plan.md`
   Data import scripts, idempotency rules, and reconciliation checks.
9. `09_quality_security_observability_plan.md`
   Testing strategy, security hardening, logging, and monitoring requirements.
10. `10_handoff_tasks_for_next_ai_agent.md`
    Actionable task board + prompts/checklists for implementation agents.
11. `appendix/`
    Baseline DB metrics, endpoint catalog, and open questions to resolve before coding.

## How to use

1. Start with `01_current_system_analysis.md` and `02_feature_inventory.md`.
2. Decide non-negotiables using `appendix/B_open_questions.md`.
3. Build Laravel skeleton from `06_laravel_target_architecture.md`.
4. Implement in the order from `07_incremental_migration_roadmap.md`.
5. Use `10_handoff_tasks_for_next_ai_agent.md` as sprint/task prompts.

## Scope baseline used for this plan

- Backend: FastAPI + SQLModel + SQLite (`backend/main.py`, `backend/services/*`, `backend/question_generator.py`).
- Frontend in production path: static jQuery app (`backend/static/index.html`, `backend/static/js/app.js`).
- Additional/legacy frontend: React scaffold in `frontend/src` (not aligned with current backend contracts).
- Database inspected: `raiida.db` at repository root.

## Important warning before migration

Hardcoded API keys/tokens exist in source files and scripts. Treat existing credentials as compromised and rotate them before any production Laravel deployment.
