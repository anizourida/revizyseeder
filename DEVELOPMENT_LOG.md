# Development Log & Specifications

> [!NOTE]
> I am now operating under the rules defined in [.agent/rules/super-prompt.md](file:///Users/macbook/Rida/ProductionRepoRevizy/revizy/.agent/rules/super-prompt.md). This ensures all future edits follow your safety and consistency requirements.

- **[Telescope Deployment Fix]** (Backend)
  - **Issue**: `file_put_contents` permission error on production when running `telescope:toggle`.
  - **Fix**: Required manual permission update on `storage/framework/cache`. Added instructions to `DEPLOYMENT.md`.

This file records all major orders and modifications requested by the USER to maintain consistency and prevent regressions in future edits.

## Order History

### 1. Project Initialization & Setup (2026-02-02)
- **Request**: Setup Laravel, link with DB, and analyze code.
- **Context**: Just pulled from GitHub. Need to ensure production safety for future updates.
- **Key Requirement**: Maintain a "spec-driven development" log to avoid bypassing previous orders in future edits.
- **Status**: [COMPLETED]

### 2. Dev Routes Implementation (2026-02-02)
- **Request**: Create dev routes to be used in dev (prod routes were defined).
- **Modification**:
    - Added `API_DOMAIN` and `ADMIN_DOMAIN` to `.env`.
    - Updated `config/app.php` to include these domains.
    - Modified `routes/api.php` to make the domain wrapper conditional.
    - Modified `SuperadminPanelProvider.php` to use `config('app.admin_domain')`.
- **Status**: [COMPLETED]

### 3. Deployment Guide Creation (2026-02-02)
- **Request**: Create a deploy.md for deployment instructions.
- **Modification**: Created [DEPLOYMENT.md](file:///Users/macbook/Rida/ProductionRepoRevizy/revizy/DEPLOYMENT.md) with step-by-step instructions.
- **Status**: [COMPLETED]

### 4. Student Seeder Implementation (2026-02-02)
- **Request**: Add seeders in students table.
- **Modification**: Updated `StudentFactory` and created `StudentSeeder` (10 random students + test user `123456`).
- **Status**: [COMPLETED]

### 5. Filament User Creation (2026-02-02)
- **Request**: Add a filament user from filament cli.
- **Modification**: Created admin user.
    - **Email**: `admin@revizyapp.com`
    - **Password**: `password123`
- **Status**: [COMPLETED]

### 6. Local Storage Configuration (2026-02-02)
- **Request**: Use local storage for development not r2.
- **Modification**:
    - Set `FILESYSTEM_DISK=public` in `.env`.
    - Replaced all hardcoded `disk('r2')` with dynamic `disk(config('filesystems.default'))` in models, resources, and jobs.
    - Updated `File` and `FileVersion` models to handle both local and R2 URLs.
    - Updated `DEPLOYMENT.md` with storage steps.
- **Status**: [COMPLETED]

### 7. Content Status Implementation (2026-02-02)
- **Request**: Add `status` column (draft, published, archived) to key content tables.
- **Modification**:
    - Created migration to add `status` column (default: 'draft') to 10+ tables (files, flashcards, discussions, subjects, unites, skills, concepts, grades, ads, categories, questions).
    - Created `PublishedScope` to filter content by status in the frontend.
    - Updated Models to apply the scope by default.
    - Updated Filament Resources to bypass the scope (`withoutGlobalScopes`) so administrators can manage all content.
- **Status**: [COMPLETED]

### 8. Filament Import & Relation Scope Fixes (2026-02-02)
- **Request**: Fix "Class SelectFilter not found" and ensure relational views show all statuses.
- **Modification**:
    - Fixed missing imports in `GradeResource.php`.
    - Systematic update of all Filament `RelationManager`s, `Select` inputs, and `Filters` to use `withoutGlobalScopes` for content models.
    - Added `PublishedScope` to `Question` model and updated `QuestionResource`.
- **Status**: [COMPLETED]

### 9. Unite Creation Fix (2026-02-02)
- **Request**: Fix "Free Open Weeks" toggles not saving during Unite creation.
- **Modification**:
    - Implemented `afterCreate` hook in `CreateUnite.php` to persist week settings from form data.
- **Status**: [COMPLETED]

### 10. Student Code Format Synchronization (2026-02-02)
- **Request**: Synchronize student code generation logic between registration, factory, and seeder using the `XX-00000` format.
- **Modification**:
    - Updated `StudentFactory` and `StudentSeeder` to use the `XX-00000` format (2 random chars + dash + 5 digits).
    - Ran a batch update script via Tinker to format all existing student codes in the database.
    - Test student code updated to `RV-12345`.
- **Status**: [COMPLETED]

### 11. Student Login UX Improvement (2026-02-02)
- **Request**: Update login to automatically insert hyphen if missing in student code (e.g., `XX12345` -> `XX-12345`).
- **Modification**:
    - Updated `StudentAuthController::login` to check if code lacks hyphen and has length > 2.
    - If condition met, inserts hyphen after the 2nd character.
    - Supports variable lengths (e.g., `XX-123456`).
- **Status**: [COMPLETED]

### 12. Media Selector Filtering (2026-02-02)
- **Request**: Only show published files in the media selector, but keep delivering archived files via API if linked.
- **Modification**:
    - Updated `MediaPickerModal.php` livewire component.
    - Added `->where('status', 'published')` to the file query.
    - Ensures archived files are hidden from selection but available via relationships.
- **Status**: [COMPLETED]

### 13. Hide Home Page & Custom Errors (2026-02-02)
- **Request**: Hide Laravel home page and custom error pages (just text).
- **Modification**:
    - **Hidden Home**: Updated `routes/web.php` to `abort(404)` on `/`.
    - **Custom Errors**: Created simple text-based blade views in `resources/views/errors/` for 401, 403, 404, 419, 429, 500, 503.
- **Status**: [COMPLETED]

### 14. File & Flashcard Default Status Update (2026-02-03)
- **Request**: Change default status for Files and Flashcards to 'published' instead of 'draft'.
- **Modification**:
    - Updated `app/Filament/Resources/FileResource.php` to set the default status to `'published'`.
    - Updated `app/Filament/Resources/FlashcardResource.php` to set the default status to `'published'`.
    - Updated `app/Filament/Resources/FlashcardCategoryResource/RelationManagers/FlashcardsRelationManager.php` which handles the "Flashcards" tab in the Flashcard Category resource, to also default to `'published'`.
- **Status**: [COMPLETED]

### 15. Color Tag Editor Custom Field (2026-02-05)
- **Request**: Create a custom Filament field with color buttons (Blue, Red, Green, Pink) to wrap selected text with tags like `[BLUE]text[/BLUE]`. Later upgraded to WYSIWYG.
- **Modification**:
    - Created `app/Forms/Components/ColorTagEditor.php` - Custom Filament field extending Textarea with configurable colors.
    - Created `resources/views/forms/components/color-tag-editor.blade.php` - WYSIWYG editor using contenteditable with:
        - Visual color display while typing (text appears colored)
        - Automatic conversion between visual HTML and `[TAG]` format on save
        - Color toolbar with Blue, Red, Green, Pink buttons
        - Clear button to remove formatting
        - Paste handling (strips rich formatting)
        - Keyboard shortcut prevention (Ctrl+B/I/U blocked)
    - Updated `app/Filament/Resources/FlashcardResource.php` to use ColorTagEditor for `front_text` and `back_text`.
    - Updated `app/Filament/Resources/QuestionResource.php` to use ColorTagEditor for question body and answer text.
    - Updated `app/Filament/Resources/FlashcardCategoryResource/RelationManagers/FlashcardsRelationManager.php` to use ColorTagEditor.
- **Status**: [COMPLETED]

### 16. Question Selection Algorithm Rewrite (2026-02-05)
- **Request**: Redesign the quiz question allocation algorithm to reduce question repetition and prioritize unplayed questions.
- **Modification**:
    - Complete rewrite of `app/Services/QuestionSelectionService.php`.
    - **New 5-Bucket Priority System** (mutually exclusive, first match wins):
        - **Bucket D (Priority 1)**: Never played questions (max 5)
        - **Bucket A (Priority 2)**: Weak concepts with mastery ≤ 60% (max 7)
        - **Bucket B (Priority 3)**: At least 1 incorrect in last 5 attempts (max 7)
        - **Bucket E (Priority 4)**: Answered incorrectly in last 14 days (max 2)
        - **Bucket C (Priority 5)**: Mastered concepts with mastery > 60% (max 7)
    - **Soft Cooldown**: Skip questions from last 1 quiz (if enough candidates remain).
    - **Concept Filtering**: Added explicit `is_active = true` check to ensure questions from disabled concepts are excluded.
    - **Ordering**: Never-played first, then by oldest-played (ASC).
    - **Fallback**: If not enough questions, add random from remaining pool.
    - Optimized with batch queries to avoid N+1 issues.
- **Key Thresholds**:
    - Weak mastery: ≤ 60%
    - Recent attempts for mistakes: last 5
    - Recent days for 14-day mistakes: last 14 days
    - Cooldown: last 1 quiz
- **Status**: [COMPLETED]

### 17. Sentry Logging Configuration (2026-02-05)
- **Request**: Configure Sentry as a log channel with environment-specific behavior (dev: file only, prod: file + Sentry).
- **Modification**:
    - Updated `config/logging.php`:
        - Modified stack to use dynamic `LOG_STACK` env variable: `explode(',', env('LOG_STACK', 'single'))`.
        - Added `sentry_logs` channel with `driver => 'sentry_logs'` and configurable log level.
    - Updated `.env` for development:
        - Added `LOG_STACK=single` (file logging only).
        - Added `SENTRY_ENABLE_LOGS=false`.
    - Updated `.env.example` with new variables and comments.
    - Updated `DEPLOYMENT.md` with production environment configuration for Sentry logging.
- **Production Env Requirements**:
    - `LOG_STACK=single,sentry_logs`
    - `LOG_LEVEL=info`
    - `SENTRY_ENABLE_LOGS=true`
- **Status**: [COMPLETED]

### 18. Color Tag Editor Relation Manager Fix (2026-02-05)
- **Request**: Fix ColorTagEditor not working in the Flashcards Relation Manager (within Flashcard Category) - component was empty in AJAX-loaded modals.
- **Root Cause**: Inline scripts in AJAX responses are not reliably executed before Alpine.js initializes components. This caused `ReferenceError` and `TypeError` when the modal opened.
- **Modification**:
    - Created `public/js/color-tag-editor.js` with all component logic.
    - Updated `app/Providers/Filament/SuperadminPanelProvider.php`:
        - Added `SCRIPTS_BEFORE` render hook to inject the JS globally on every page.
    - Simplified `resources/views/forms/components/color-tag-editor.blade.php`:
        - Removed all inline scripts.
        - Changed `x-data` to directly call `window.colorTagEditor()`.
- **Status**: [COMPLETED]


### 19. Styled Question Preview (2026-02-05)
- **Request**: Add style (color) to question preview from ColorTagEditor.
- **Modification**:
    - Updated `resources/views/preview.blade.php`:
        - Added `parseColorTags` helper to transform `[COLOR]` tags into styled HTML.
        - Configured to use text color (e.g. `color: #...`) and bold weight instead of background color.
        - Applied this parsing to Question Body and Answer Body.
- **Status**: [COMPLETED]

### 20. Unite Resource Update (2026-02-05)
- **Request**: Remove reorderable from Unite resource table but keep index visible. Add index input to Unite form.
- **Modification**:
    - Updated `app/Filament/Resources/UniteResource.php`.
    - Removed `->reorderable('index')` from table builder.
    - Added `Forms\Components\TextInput::make('index')` to form schema in "La periode d'apprentissage" section.
- **Status**: [COMPLETED]

### 21. Unite Ordering Implementation (2026-02-06)
- **Request**: Ensure API returns Unites ordered by the `index` column.
- **Modification**:
    - Updated `app/Models/Subject.php`:
    - Modified `unites()` relationship to add `->orderBy('index')`.
    - This ensures `SubjectController::unites` and other endpoints return units in the correct custom order.
- **Status**: [COMPLETED]

### 22. Hide FCM Token (2026-02-06)
- **Request**: In student model make fcm_token hidden.
- **Modification**:
    - Updated `app/Models/Student.php` to add `fcm_token` to the `$hidden` array.
- **Status**: [COMPLETED]

### 23. API Backward Compatibility Rule (2026-02-06)
- **Request**: Ensure strict backward compatibility for API responses.
- **Rule**:
    - **ADDITIVE ONLY**: New fields can be added.
    - **NO DELETIONS**: Existing fields must NOT be removed or renamed without explicit user confirmation.
    - **Context**: Active users exist on older app versions that rely on the current response structure.
- **Status**: [PERMANENT]

### 24. Flashcard Categories API Update (2026-02-06)
- **Request**: Add `updated_at` to the `/api/v1/flashcard-categories` response.
- **Modification**:
    - Updated `App\Http\Controllers\Api\V1\FlashcardCategoryController` `formatCategory` method.
    - Added `updated_at` field to the return array.
- **Status**: [COMPLETED]

### 25. Flashcards API Update (2026-02-06)
- **Request**: Add `updated_at` to the `/api/v1/flashcard-categories/{categoryId}/flashcards` response.
- **Modification**:
    - Updated `App\Http\Controllers\Api\V1\FlashcardController` `index` method.
    - Added `updated_at` field to the return array.
- **Status**: [COMPLETED]

### 26. Smart Content Caching (2026-02-07)
- **Request**: Implement version-based caching to minimize bandwidth and server costs.
- **Modification**:
    - **Models**: Added `$touches` to `Flashcard`, `FlashcardCategory` (touches parent), and `Discussion` to propagate `updated_at` timestamps to top-level categories.
    - **API**: Created `ContentVersionController` (`GET /api/v1/content/versions`) to return `updated_at` timestamps for Flashcards and Discussions, filtered by `grade_id`.
    - **Auth**: Endpoint is protected by `auth:sanctum`.
    - **Backward Compatibility**: Fully additive change; old apps continue to work.
- **Status**: [IN_PROGRESS]



- **[Fix]** Timezone Synchronization Issue (2026-02-07):
  - **Issue**: Backend returned raw SQL timestamps (`2026-02-07 03:47:20`) while resource APIs returned ISO 8601 UTC (`2026-02-07T02:47:20.000000Z`), causing frontend comparison failures.
  - **Fix**: Updated `ContentVersionController.php` to return ISO 8601 UTC timestamps using `Carbon::parse($date)->toISOString()`. 
  - **Frontend**: Updated `contentSync.js` to parse dates before comparison.
  - **Frontend**: Implemented Unified Logging System (`utils/logger.js`) and added Pull-to-Refresh to Flashcards and Discussions.

### 27. Answer Checking Logic Fix (2026-02-09)
- **Request**: Fix issue where correct answers were marked as wrong due to case sensitivity (e.g., "LA SOLUTION" vs "La solution").
- **Modification**:
    - Updated `App\Http\Controllers\Api\V1\QuizController` `findMatchingAnswer` method.
    - Implemented strict case-insensitive comparison using `mb_strtolower`.
    - Added logic to strip custom color tags (e.g., `[BLUE]...[/BLUE]`) from stored answers before comparison.
- **Status**: [COMPLETED]

### 28. Weekly Mastery Active Concept Filter (2026-02-09)
- **Request**: Ensure weekly mastery calculation only includes active concepts.
- **Modification**:
    - Updated `App\Services\WeeklyMasteryService` `getWeeklyMastery` method.
    - Added `->where('concepts.is_active', true)` to the query.
    - This ensures that concepts marked as inactive (even if they have published questions) do not lower the mastery score (by increasing total count) or appear in the calculation.
- **Status**: [COMPLETED]
### 29. Styled Answer Checking Fix (2026-02-09)
- **Request**: Fix issue where user answers with style tags (e.g., `[PINK]Une[/PINK]`) were marked incorrect.
- **Modification**:
    - Updated `App\Http\Controllers\Api\V1\QuizController` `findMatchingAnswer` method.
    - Applied tag stripping regex to user input before normalization and comparison.
    - Ensures `[PINK]Answer[/PINK]` matches `Answer`.
- **Status**: [COMPLETED]

### 30. Question Selection Algorithm Update (2026-02-09)
- **Request**: Fix bucket ordering and update thresholds.
- **Modification**:
    - Updated `App\Services\QuestionSelectionService.php`.
    - **Bucket Limits**: Changed A (weak concepts) and B (recent mistakes) max from 7 to 5.
    - **Threshold**: Changed weak/mastered boundary from 60% to 70%.
    - **Ordering Fix**: Modified `orderCandidates` to sort by bucket priority first, then by oldest-played within the same bucket. This ensures Bucket A (weak concepts) questions always appear before Bucket C (mastered) questions.
- **Status**: [COMPLETED]

### 31. Unite Subject Filter Label Update (2026-02-13)
- **Request**: Update the Subject filter in UniteResource to show the grade name alongside the subject name.
- **Modification**:
    - Updated `app/Filament/Resources/UniteResource.php`.
    - Changed `getOptionLabelFromRecordUsing` on the Subject `SelectFilter` from `name — description` to `name (grade.name)`.
    - This makes it easier to distinguish subjects that share the same name across different grades.
- **Status**: [COMPLETED]

### 32. System API: Create Questions (2026-02-14)
- **Request**: Add a system API endpoint to create questions.
- **Modification**:
    - Created `app/Http/Controllers/Api/System/SystemQuestionController.php` with `store` method.
    - Validates `concept_id`, `name`, `description`, `difficulty`, `type`, `status`, and `data` (JSON).
    - Verifies concept exists (bypassing `PublishedScope`).
    - Registered `POST /api/system/questions` route in `routes/api.php` under the system middleware group.
- **Status**: [COMPLETED]

### 33. Questions Resource: Bulk Actions (2026-02-18)
- **Request**: Add "Unpublish" and "Publish" bulk actions to the Question resource.
- **Modification**:
    - Updated `app/Filament/Resources/QuestionResource.php`.
    - Added `Illuminate\Database\Eloquent\Collection` to imports.
    - Implemented `publish` bulk action to set status to `'published'`.
    - Implemented `unpublish` bulk action to set status to `'unpublished'`.
    - Both actions require confirmation and deselect records after completion.
- **Status**: [COMPLETED]

### 34. Question Validation Layer (2026-02-18)
- **Request**: Implement data integrity validation for questions (structural, pedagogical, duplicates) using `QuestionValidator`.
- **Modification**:
    - **Service**: Created `App\Services\QuestionValidator` to validate structure, minimum answers, duplicates, and pedagogical rules (French elision, gender questions).
    - **Model**: Updated `Question` model `booted` method to enforce `QuestionValidator::validate()` on `saving` event. Blocks saving invalid data.
    - **Command**: Created `AuditQuestions` artisan command (`questions:audit`) to scan all questions and report errors without modifying data.
    - **UI**: Created `QuestionQuality` Filament page (`Studio > Question Quality`) and `QuestionQualityStats` widget to visualize integrity metrics.
- **[Fix]** Audit Command Permission Handler (2026-02-18):
    - **Issue**: `questions:audit` failed on production due to `file_put_contents` permission error when writing to cache.
    - **Fix**: Wrapped `Cache::put` in a `try-catch` block in `AuditQuestions.php` to log a warning instead of crashing the process.
- **Status**: [COMPLETED]
|
### 35. Dashboard Cleanup (2026-02-19)
- **Request**: Remove Question Quality UI from the main dashboard but keep it on the Question Quality page.
- **Modification**:
    - Created `App\Filament\Pages\Dashboard` extending the base Filament Dashboard.
    - Overrode `getWidgets()` to filter out `QuestionQualityStats` and `InvalidQuestionsTable`.
    - Updated `SuperadminPanelProvider` to use the custom `Dashboard` page class.
- **Status**: [COMPLETED]

### 36. Enhanced Question Quality System (2026-02-19)
- **Request**: Persist question validation errors, allow ignoring specific errors, and integrate with UI.
- **Modification**:
    - **Database**: Created `question_audits` table to store validation errors interactively.
    - **Model**: Created `QuestionAudit` model.
    - **Service**: Refactored `QuestionValidator` to return structured errors (code + message).
    - **Command**: Updated `AuditQuestions` to sync errors with the database (creating/updating/deleting records).
    - **Background Job**: Created `RunQuestionAuditJob` to run the audit asynchronously.
    - **UI**: 
        - Updated `QuestionQualityStats` to read from the new data structure.
        - Updated `InvalidQuestionsTable` to show structured errors and added "Manage Errors" action to ignore specific issues.
        - Added "Run Full Audit" action to the Question Quality page.
- **Status**: [COMPLETED]

- **[Fix]** Question Audits Relationship Error (2026-02-19):
    - **Issue**: 500 Error on `livewire/update` in Question Quality page due to missing `audits` relationship in `Question` model.
    - **Fix**: Added `audits()` hasMany relationship to `app/Models/Question.php`.

### 37. Flashcard Categories Ordering (2026-02-19)
- **Request**: Always order Flashcard Categories by `order_index` in the Filament resource list.
- **Modification**:
    - Updated `app/Filament/Resources/FlashcardCategoryResource.php`:
        - Changed `defaultSort` from `id` (desc) to `order_index` (asc).
        - Added `Index` column to the table for visibility and manual sorting.
- **Status**: [COMPLETED]

### 38. File resource edit secret_id (2026-02-20)
- **Request**: Add a disabled input field for secret_id in the edit mode of the File resource.
- **Modification**:
    - Updated `app/Filament/Resources/FileResource.php`.
    - Added a disabled `TextInput` for `secret_id` visible only in edit mode via `visible(fn($context) => $context === 'edit')`.
- **Status**: [COMPLETED]

### 39. Question Article Capitalization Rule (2026-02-20)
- **Request**: Require article words (une/un/le/la) at the start of answers to be capitalized.
- **Modification**:
    - Updated `app/Services/QuestionValidator.php`.
    - Added a pedagogical regex check `preg_match('/^(un|une|le|la)\b/u')`.
    - Throws `UNCAPITALIZED_ARTICLE` error if the answer starts with lowercase instances of these articles.
- **Status**: [COMPLETED]

### 40. Fix Color Tag Resurgence on Save (2026-02-20)
- **Request**: Stop deleted color formatting (using the Clear button) from reappearing automatically when uploaded/saved.
- **Modification**:
    - Updated `app/Models/Question.php` inside the `saving` boot event.
    - Check `$question->getOriginal('data')` against incoming `data`.
    - If the user explicitly stripped formatting from an answer (or question body) but left the text otherwise identical, the system will *not* re-apply automatic styling (e.g. `[PINK]La[/PINK]`).
- **Status**: [COMPLETED]

### 41. System API: File Upload Status (2026-02-21)
- **Request**: Allow setting the status (draft, published, archived) of files uploaded via the System API.
- **Modification**:
    - Updated `App\Http\Controllers\Api\System\SystemFileController.php`.
    - Added `status` validation rule `nullable|string|in:draft,published,archived` to `store` and `update` methods.
    - Updated `store` to set `$file->status` from the request, defaulting to `File::STATUS_DRAFT`.
    - Updated `update` to conditionally set `$file->status` if provided in the request.
- **Status**: [COMPLETED]

### 42. Answer Checking Apostrophe Normalization (2026-02-21)
- **Request**: Allow both straight (`'`) and curly (`’`) apostrophes in text-based answers (e.g. fill text) so answers aren't marked wrong due to keyboard differences.
- **Modification**:
    - **Frontend**: Updated `QuizScreen.jsx` `handleValidate` method to normalize `’` to `'` in both the user input and the correct answers for `translate_input` and `letter_by_letter` types before comparison.
    - **Backend**: Updated `App\Http\Controllers\Api\V1\QuizController` `findMatchingAnswer` method to normalize `’`, `‘`, and `` ` `` to `'` in both the user answer and database answers prior to comparison.
- **Status**: [COMPLETED]

### 43. Fix Content Versions Not Reflecting Discussion Variant/Message Edits (2026-02-24)
- **Request**: `/api/v1/content/versions` was not returning updated timestamps after editing Discussion Variants or Messages.
- **Root Cause**: The `$touches` chain was broken below `Discussion` level:
    - `DiscussionVariant` had `$timestamps = false` and no `$touches`.
    - `DiscussionMessage` had no `$touches`.
    - Server-side cache TTL was 5 minutes.
- **Modification**:
    - Updated `app/Models/DiscussionVariant.php`: Removed `$timestamps = false`, added `$touches = ['discussion']`.
    - Updated `app/Models/DiscussionMessage.php`: Added `$touches = ['variant']`.
    - Updated `app/Http/Controllers/Api/V1/ContentVersionController.php`: Reduced cache TTL from 300s to 60s.
    - Created migration `2026_02_24_210000_add_updated_at_to_discussion_variants_table.php` to add the missing `updated_at` column.
- **Touch Chain**: `DiscussionMessage` → `DiscussionVariant` → `Discussion` → `DiscussionCategory` (fully propagated).
- **Status**: [COMPLETED]

### 44. System API: Concept Detail Endpoint (2026-02-25)
- **Request**: Add a system API endpoint to retrieve concept details including associated skill and unit.
- **Modification**:
    - Updated `app/Http/Controllers/Api/System/SystemConceptController.php` to add `show` method.
    - The `show` method returns the concept with its `skill` and `unite` relationships, bypassing `PublishedScope`.
    - Registered `GET /api/system/concepts/{id}` route in `routes/api.php` under the system middleware group.
- **Status**: [COMPLETED]

### 45. Conjugaison Lessons Inline Filters (2026-03-05)
- **Request**: Make the Grade, Period, and Week filters in Conjugaison Lessons inline.
- **Modification**:
    - Updated `app/Filament/Resources/ConjugaisonResource.php`.
    - Added `layout: \Filament\Tables\Enums\FiltersLayout::AboveContent` to the `->filters()` method.
- **Status**: [COMPLETED]

### 46. Conjugaison Lessons UI Update (2026-03-05)
- **Request**: Hide the Question column and add a "View Raw" action button in the Conjugaison Lessons resource.
- **Modification**:
    - Updated `app/Filament/Resources/ConjugaisonResource.php`.
    - Added `->hidden()` to the `question` column in the table schema.
    - Added a custom action `view_raw` that opens a modal to view the `raw_data` of the lesson.
    - Created a simple Blade view `resources/views/filament/pages/actions/view-raw-json.blade.php` to display the raw JSON data.
- **Status**: [COMPLETED]

### Download Hangs Fix
- **Request**: Fix the issue where downloads get stuck at "Downloading (X.XX MB)".
- **Modification**: Updated `app/Services/Raiida/SyncFilesService.php` to configure Guzzle/cURL with `CURLOPT_LOW_SPEED_LIMIT` and `CURLOPT_LOW_SPEED_TIME` so that stalled downloads abort instead of hanging indefinitely. Increased the timeout to 600 seconds to allow for very large stable downloads.
- **Status**: [COMPLETED]

### Fetch Failed Downloads & Filtration UI
- **Request**: Include failed/stopped downloads in the 'Refresh' fetch action and list them visually.
- **Modification**: 
  - Updated `SyncFilesService.php` and `SyncFilesJob.php` to receive a `$retryPermanentErrors` parameter.
  - Added a toggle 'Retry previously failed downloads' to the Filament `FilesResource` Fetch modal.
  - Added `Download Status` select filter to `FilesResource.php` for simpler triaging of failed or pending files.
- **Status**: [COMPLETED]

### Download Hangs Fix
- **Request**: Fix the issue where downloads get stuck at "Downloading (X.XX MB)".
- **Modification**: Updated `app/Services/Raiida/SyncFilesService.php` to configure Guzzle/cURL with `CURLOPT_LOW_SPEED_LIMIT` and `CURLOPT_LOW_SPEED_TIME` so that stalled downloads abort instead of hanging indefinitely. Increased the timeout to 600 seconds to allow for very large stable downloads.
- **Status**: [COMPLETED]

### 47. Comprehensive Conjugaison Raw Data Extraction (2026-03-06)
- **Request**: Extract ALL conjugaison-related raw data from lesson presentation files for a given scope (N/P/SEM) and store it as structured data to feed to AI for question generation.
- **Modification**:
    - Created `app/Services/Raiida/ConjugaisonRawDataExtractor.php`: New service that scans FR lesson session directories, reads `data.json` files, and applies smart pattern matching to extract all conjugaison-related text (fill-in-the-blank, conjugated sentences, verb fragments, exercise instructions, corrections, lesson objectives) while filtering out teacher notes, navigation, and non-conjugaison content.
    - Created `app/Console/Commands/ExtractConjugaisonRawDataCommand.php`: Artisan command `revizyseeder:extract-conjugaison-raw-data` with `--n`, `--p`, `--sem`, and `--dry-run` options.
    - Data is stored in `raw_data` (plain text, deduplicated lines) and `related_raw_data` (structured JSON with session/slide metadata and type classification) in the `conjugaisons` table.
    - Tested on N4/P1/SEM1: extracted 85 matches (56 unique lines) across 5 sessions (S2, S3_V2, S4, S5, S6).
- **Key Difference from Existing Extraction**: The original `ConjugaisonExtractionService` picks the single best conjugaison candidate per scope. This new extractor collects ALL conjugaison-related content for AI consumption.
- **Status**: [COMPLETED]

### 48. Conjugaison Questions Creator UI (2026-03-07)
- **Request**: Add a questions creator to the Conjugaison Studio in the Raiida UI, with JSON textarea input, question preview cards with Publish/Unaccept buttons, and integration with the existing Revizy question publishing API.
- **Modification**:
    - Updated `resources/views/raiida/partials/main-content.blade.php`: Replaced static Questions JSON section with a hidden `conj-questions-modal` that opens from conjugaison row buttons,  with concept ID auto-fill, JSON textarea, and preview area.
    - Updated `public/raiida-ui/js/app.js`: Added "Questions" button to each conjugaison table row (requires concept_id). Full JSON parser using `renderQuestionPreviewCard()` with Publish/Unaccept buttons. Concept auto-verification on modal open. Publish/Unaccept handlers wired to existing `/questions/{id}/publish` and `/questions/{id}/unaccept` backend endpoints.
    - Also added `--all` flag to `ExtractConjugaisonRawDataCommand` for batch extraction across all N1-N6/P1-P5/SEM1-SEM6 scopes. Updated `ConjugaisonRawDataExtractor::findSessionDirectories` to match multi-grade directories (e.g. `FR_N3&4_*`).
    - Updated raw data modal view (`view-raw-json.blade.php`) to display content line-by-line instead of JSON-encoded text.
- **No backend changes needed**: Reuses existing `QuestionPublishController` for publish/unaccept.
- **Status**: [COMPLETED]

### 49. Conjugaison Concept ID Management (2026-03-07)
- **Request**: Add concept_id column to Filament conjugaison table. Add "Create Concept" form inside the ManageConjugaisonQuestions page with auto-filled fields. Save concept_id back to the conjugaison record.
- **Modification**:
    - Added `concept_id` badge column to `ConjugaisonResource` Filament table (green when set, gray when none).
    - Added `updateConcept()` to `ConjugaisonController` and `PATCH /api/conjugaison/{id}/concept` route.
    - Rewrote `conjugaison-questions.blade.php` with Select/Create concept tabs, auto-filled Create form (verbe+tense name, grade skill_id mapping: N1→11, N2→10, N3→12, N4→2, N5→13, N6→14, week from SEM), concept saved to DB on create/link.
- **Status**: [COMPLETED]

## Edit #N (2026-03-09)
- **Description:** Created `Page` model, table, and Filament `PageResource`. Added `RevizySeederExtractPagesCommand` to parse presentation JSON and extract 'Contenu de la semaine' slide images as pages.
- **Files Changed:** `app/Models/Raiida/Page.php`, `database/migrations/2026_03_09_232323_create_pages_table.php`, `app/Filament/Resources/PageResource.php`, `app/Console/Commands/RevizySeederExtractPagesCommand.php`
