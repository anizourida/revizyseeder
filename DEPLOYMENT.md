# RevizySeeder Deployment Guide

This guide is for the isolated Raiida app at:
`/Users/macbook/Rida/ProductionRepoRevizy/Seeder`

## 1) Runtime Requirements

- PHP `8.4+` (required by installed dependencies)
- Composer `2.x`
- Python `3.10+` with `python-pptx` installed
- SQLite (or update `config/database.php` + `.env` for another DB engine)
- Queue worker for `database` queue connection

If your shell resolves another PHP version, run artisan/composer with Herd PHP explicitly:

```bash
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan --version
"/Users/macbook/Library/Application Support/Herd/bin/php" /usr/local/bin/composer --version
```

## 2) Environment Setup

Create `.env` from `.env.example` and set:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/Seeder/database/database.sqlite

QUEUE_CONNECTION=database

RAIIDA_SOURCE_SQLITE_PATH=/absolute/path/to/Seeder/database/source/raiida.db
RAIIDA_SOURCE_STATIC_PATH=/absolute/path/to/Seeder/storage/source-static
RAIIDA_FILES_ROOT=/absolute/path/to/Seeder/files
RAIIDA_VOCAB_ASSETS_ROOT=/absolute/path/to/Seeder/public/vocab_assets
RAIIDA_PRESENTATION_PYTHON_BIN=python3
RAIIDA_PRESENTATION_SCRIPT_PATH=/absolute/path/to/Seeder/scripts/extract_lesson_data.py
RAIIDA_PRESENTATION_OUTPUT_ROOT=/absolute/path/to/Seeder/storage/app/presentation_data
RAIIDA_PRESENTATION_PROCESS_TIMEOUT=300
RAIIDA_PRESENTATION_QUEUE=revizyseeder-workflows
RAIIDA_PRESENTATION_FILE_LOCK_SECONDS=1800
RAIIDA_PRESENTATION_AUTO_EXTRACT_AFTER_DOWNLOAD=true

REVIZY_BASE_URL=https://admin.revizyapp.com/api/system
REVIZY_API_KEY=your_key
WALIDIO_BASE_URL=https://walidio.online/api
WALIDIO_PUBLIC_KEY=your_walidio_key
RAIIDA_AUDIO_GENERATOR_ENABLED=false
```

## 3) Install and Bootstrap

```bash
cd /Users/macbook/Rida/ProductionRepoRevizy/Seeder
"/Users/macbook/Library/Application Support/Herd/bin/php" /usr/local/bin/composer install --no-dev --optimize-autoloader
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan key:generate
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan migrate --force
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan raiida:import --source=/absolute/path/to/Seeder/database/source/raiida.db
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan revizyseeder:hydrate-files --source=/Users/macbook/Rida/fichiers-raiida/files --mode=copy
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan revizyseeder:extract-presentation-data --queue
```

If you already have all legacy files locally, run `revizyseeder:hydrate-files` to avoid downloading everything again.
Recommended fast mode (same disk): `--mode=hardlink`
After files are available locally, run `revizyseeder:extract-presentation-data` to generate `data.json` + extracted media assets for each downloaded PPT file.

Create an admin/operator account:

```bash
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan raiida:operator:create --name="RevizySeeder Admin" --email="admin@revizyseeder.local" --password="Secret123!" --role=admin --force
```

## 4) Queue Worker

```bash
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan queue:work --queue=revizyseeder-workflows,default --tries=3 --timeout=120
```

Use Supervisor/systemd in production.

## 5) Verification Checklist

```bash
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan route:list --path=api
"/Users/macbook/Library/Application Support/Herd/bin/php" artisan test
```

Expected key routes:
- `POST /api/auth/login`
- `GET /api/stats`
- `GET /api/files`
- `GET /api/tree`
- `GET /api/vocabulary`
- `GET /api/vocabulary-assets`
- `GET /api/questions/counts`
- `GET /api/proxy/*`
- `POST /api/vocabulary-assets/{id}/upload-*`
- `GET /admin` (Filament dashboard)
- `GET /raiida/dashboard`
- `GET /raiida/questions-studio`
- `GET /raiida/roadmap`
- `GET /raiida/grammaire`

## 6) Production Hardening

- Set strong real credentials for operator/admin users.
- Keep `REVIZY_API_KEY` and all keys only in environment variables.
- Use `php artisan optimize` after deployment.
- Ensure `storage` and `bootstrap/cache` are writable by web/worker users.
