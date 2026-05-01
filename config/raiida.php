<?php

return [
    'workflow_queue' => env('REVIZYSEEDER_WORKFLOW_QUEUE', 'revizyseeder-workflows'),

    'source_sqlite_path' => env('RAIIDA_SOURCE_SQLITE_PATH', database_path('source/raiida.db')),
    'source_static_path' => env('RAIIDA_SOURCE_STATIC_PATH', ''),

    'files_root' => env('RAIIDA_FILES_ROOT', base_path('files')),
    'vocab_assets_root' => env('RAIIDA_VOCAB_ASSETS_ROOT', public_path('vocab_assets')),

    'sync' => [
        'metadata_url' => env('RAIIDA_SYNC_METADATA_URL', 'https://content-management.dice.ma/api/v1/pptx/listDesktop'),
        'file_base_url' => env('RAIIDA_SYNC_FILE_BASE_URL', 'https://streameo.dice.ma/slow/ppt/'),
        'lock_key' => env('REVIZYSEEDER_FETCH_LOCK_KEY', 'revizyseeder-sync-files'),
        'lock_seconds' => (int) env('REVIZYSEEDER_FETCH_LOCK_SECONDS', 7200),
        'download_batch_name' => env('REVIZYSEEDER_FETCH_BATCH_NAME', 'revizyseeder-fetch-downloads'),
        'download_batch_chunk_size' => (int) env('REVIZYSEEDER_FETCH_BATCH_CHUNK_SIZE', 300),
        'file_lock_seconds' => (int) env('REVIZYSEEDER_FILE_LOCK_SECONDS', 1800),
        'user_agent' => env(
            'RAIIDA_SYNC_USER_AGENT',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) TelmidTice/1.0.9 Chrome/142.0.7444.59 Electron/39.1.1 Safari/537.36'
        ),
        'app_key' => env('RAIIDA_SYNC_APP_KEY'),
    ],

    'vocabulary' => [
        'marker_phrase' => env('RAIIDA_VOCAB_MARKER_PHRASE', 'qui veut répéter'),
        'subject_whitelist' => ['Français', 'French', 'FR'],
        'grade_whitelist' => ['1', '2', '3', '4', '5', '6'],
        'session_for_global_extraction' => env('RAIIDA_VOCAB_SESSION', 'S1'),
        'retry_zero_count_lessons' => (bool) env('RAIIDA_VOCAB_RETRY_ZERO_COUNT', false),
        'text_exclusions' => ['objectifs', 'enseignant', 'date', 'semaine', 'titre'],
    ],

    'presentation_data' => [
        'python_bin' => env('RAIIDA_PRESENTATION_PYTHON_BIN', 'python3'),
        'script_path' => env('RAIIDA_PRESENTATION_SCRIPT_PATH', base_path('scripts/extract_lesson_data.py')),
        'output_root' => env('RAIIDA_PRESENTATION_OUTPUT_ROOT', storage_path('app/presentation_data')),
        'process_timeout_seconds' => (int) env('RAIIDA_PRESENTATION_PROCESS_TIMEOUT', 300),
        'queue' => env('RAIIDA_PRESENTATION_QUEUE', env('REVIZYSEEDER_WORKFLOW_QUEUE', 'revizyseeder-workflows')),
        'file_lock_seconds' => (int) env('RAIIDA_PRESENTATION_FILE_LOCK_SECONDS', 1800),
        'auto_extract_after_download' => (bool) env('RAIIDA_PRESENTATION_AUTO_EXTRACT_AFTER_DOWNLOAD', true),
    ],

    'conjugaison_extraction' => [
        'queue' => env('RAIIDA_CONJUGAISON_QUEUE', env('REVIZYSEEDER_WORKFLOW_QUEUE', 'revizyseeder-workflows')),
        'target_subject_prefix' => env('RAIIDA_CONJUGAISON_TARGET_SUBJECT_PREFIX', 'FR_'),
        'grade_range' => [1, 2, 3, 4, 5, 6],
        'period_range' => [1, 2, 3, 4, 5],
        'week_range' => [1, 2, 3, 4, 5, 6],
    ],

    'revizy' => [
        'base_url' => env('REVIZY_BASE_URL', 'https://admin.revizyapp.com/api/system'),
        'api_key' => env('REVIZY_API_KEY'),
    ],

    'walidio' => [
        'base_url' => env('WALIDIO_BASE_URL', 'https://walidio.online/api'),
        'public_key' => env('WALIDIO_PUBLIC_KEY'),
    ],

    'audio_generator' => [
        'enabled' => (bool) env('RAIIDA_AUDIO_GENERATOR_ENABLED', false),
        'provider_slug' => env('RAIIDA_AUDIO_PROVIDER_SLUG', 'typecast'),
        'wait_seconds_between_items' => (int) env('RAIIDA_AUDIO_WAIT_SECONDS', 10),
        'typecast' => [
            'base_url' => env('TYPECAST_BASE_URL', 'https://typecast.ai'),
            'post_endpoint' => env('RAIIDA_AUDIO_TYPECAST_POST_ENDPOINT', 'https://typecast.ai/api/speak/batch/post'),
            'get_endpoint' => env('RAIIDA_AUDIO_TYPECAST_GET_ENDPOINT', 'https://typecast.ai/api/speak/batch/get'),
            'actor_id' => env('RAIIDA_AUDIO_TYPECAST_ACTOR_ID', '64f97820ffc5b7a301bf119e'),
            'lang' => env('RAIIDA_AUDIO_TYPECAST_LANG', 'fra'),
            'poll_attempts' => (int) env('RAIIDA_AUDIO_TYPECAST_POLL_ATTEMPTS', 60),
            'poll_interval_ms' => (int) env('RAIIDA_AUDIO_TYPECAST_POLL_INTERVAL_MS', 1000),
            'request_timeout_seconds' => (int) env('RAIIDA_AUDIO_TYPECAST_TIMEOUT_SECONDS', 90),
            'referer' => env('RAIIDA_AUDIO_TYPECAST_REFERER', 'https://typecast.ai/text-to-speech/698a59a7ce61a36e23ee15ca'),
            'origin' => env('RAIIDA_AUDIO_TYPECAST_ORIGIN', 'https://typecast.ai'),
            'user_agent' => env(
                'RAIIDA_AUDIO_TYPECAST_USER_AGENT',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36'
            ),
        ],
    ],

    'concept_generator' => [
        'wait_ms_between_items' => (int) env('RAIIDA_CONCEPT_WAIT_MS', 200),
        'debug_search' => (bool) env('RAIIDA_CONCEPT_DEBUG_SEARCH', false),
    ],

    'deepl' => [
        'api_key' => env('DEEPL_API_KEY'),
        'base_url' => env('DEEPL_BASE_URL'),
        'usage_endpoint' => env('DEEPL_USAGE_ENDPOINT'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),
        'api_version' => env('GEMINI_API_VERSION', 'v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 1400),
        'classification_batch_size' => (int) env('GEMINI_CLASSIFICATION_BATCH_SIZE', 40),
        'monthly_token_limit' => env('GEMINI_MONTHLY_TOKEN_LIMIT'),
    ],

    'api_providers' => [
        'builtins' => [
            'deepl' => [
                'provider_type' => 'deepl',
                'display_name' => 'DeepL',
                'api_key' => env('DEEPL_API_KEY'),
                'base_url' => env('DEEPL_BASE_URL'),
                'usage_endpoint' => env('DEEPL_USAGE_ENDPOINT'),
                'limit_unit' => 'characters',
                'monthly_limit' => env('DEEPL_MONTHLY_CHARACTER_LIMIT'),
                'is_active' => true,
            ],
            'gemini' => [
                'provider_type' => 'gemini',
                'display_name' => 'Gemini',
                'api_key' => env('GEMINI_API_KEY'),
                'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),
                'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
                'limit_unit' => 'tokens',
                'monthly_limit' => env('GEMINI_MONTHLY_TOKEN_LIMIT'),
                'is_active' => true,
                'metadata' => [
                    'gemini_api_version' => env('GEMINI_API_VERSION', 'v1beta'),
                ],
            ],
            'typecast' => [
                'provider_type' => 'typecast',
                'display_name' => 'Typecast TTS',
                'api_key' => env('TYPECAST_AUTHORIZATION'),
                'auth_cookie' => env('TYPECAST_COOKIE'),
                'base_url' => env('TYPECAST_BASE_URL', 'https://typecast.ai'),
                'model' => env('RAIIDA_AUDIO_TYPECAST_ACTOR_ID', '64f97820ffc5b7a301bf119e'),
                'limit_unit' => 'requests',
                'monthly_limit' => env('TYPECAST_MONTHLY_REQUEST_LIMIT'),
                'is_active' => (bool) env('TYPECAST_ACTIVE', false),
                'metadata' => [
                    'actor_id' => env('RAIIDA_AUDIO_TYPECAST_ACTOR_ID', '64f97820ffc5b7a301bf119e'),
                    'lang' => env('RAIIDA_AUDIO_TYPECAST_LANG', 'fra'),
                    'referer' => env('RAIIDA_AUDIO_TYPECAST_REFERER', 'https://typecast.ai/text-to-speech/698a59a7ce61a36e23ee15ca'),
                ],
            ],
        ],
    ],
];
