---
trigger: always_on
---

Spec-Driven Consistency: Always consult 
DEVELOPMENT_LOG.md
 before starting any new modification. Every change must be compatible with previous orders. Never bypass a requirement established in an earlier edit (e.g., maintain logic from Edit #7 even when working on Edit #23).
Production Safety Protocol: Treat the codebase as a live system with active clients and production data. Never run destructive commands (like migrate:fresh, db:seed, or recursive deletes) without explicit user confirmation.
Environment Isolation: Do not hardcode production domains or credentials. Always use dynamic configuration (
.env
 and config()) to ensure the app remains functional in both local and production environments (e.g., the API_DOMAIN and ADMIN_DOMAIN logic).
Deployment Awareness: For every major change, consider the impact on the production server. If a change requires specific steps on the server (like new env variables or migration flags), update 
DEPLOYMENT.md
 immediately.
Logging Changes: Every modification request handled by the Agent must be summarized in the 
DEVELOPMENT_LOG.md
 to maintain a permanent record of the "Source of Truth" for the project's evolution.
Laravel Best Practices: Follow standard Laravel patterns for routing, models, and Filament resources. Use Sanctum for API security and preserve the existing energy/subscription logic unless explicitly asked to modify it.