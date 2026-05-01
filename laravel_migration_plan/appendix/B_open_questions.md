# Appendix B - Open Questions to Resolve Before Build

## Product/operations

1. Should grade filtering remain FR-only in certain endpoints, or become configurable?
2. Should legacy React frontend be dropped entirely or re-aligned later?
3. Is `question` table still required by any external consumer?
4. Should N4 skip in auto-sync remain a business rule or was it temporary?

## Data

5. Which DB is canonical if there are multiple `raiida.db` copies?
6. Do we keep legacy IDs exactly in Laravel DB or remap with lookup tables?
7. Should combined grades (`N1&2`) remain skipped permanently?
8. Should conjugaison/grammaire be managed manually post-migration or auto-extracted regularly?

## Integrations

9. Are Revizy and Walidio endpoints stable and versioned?
10. Do we need request signatures/idempotency headers for external writes?
11. Are there API rate limits requiring queue throttling?

## Security

12. Which user roles are required in production?
13. Should read endpoints be public or authenticated in final system?
14. Where will secrets be stored (Vault, Doppler, AWS/GCP Secret Manager, etc.)?

## Infrastructure

15. Target deployment stack for Laravel (Nginx + PHP-FPM + Redis + MySQL/Postgres)?
16. Required uptime and RTO/RPO for backup strategy?
17. Do we need horizontal queue workers from day one?
