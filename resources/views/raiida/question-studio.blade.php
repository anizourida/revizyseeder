<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Raiida Question Studio</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap');

        :root {
            --bg-1: #f4f7f8;
            --bg-2: #dce7ea;
            --ink: #17313a;
            --muted: #57707a;
            --panel: #ffffff;
            --line: #d9e4e8;
            --accent: #ff7a59;
            --accent-ink: #4b1608;
            --ok: #1e9f6e;
            --warn: #d37c00;
            --danger: #ce3e32;
            --shadow: 0 16px 40px rgba(17, 49, 58, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            color: var(--ink);
            font-family: "Manrope", "Trebuchet MS", sans-serif;
            background:
                radial-gradient(circle at 10% 10%, rgba(255, 122, 89, 0.20), transparent 32%),
                radial-gradient(circle at 90% 12%, rgba(30, 159, 110, 0.22), transparent 28%),
                linear-gradient(155deg, var(--bg-1), var(--bg-2));
        }

        .shell {
            width: min(1440px, calc(100% - 40px));
            margin: 24px auto 36px;
            display: grid;
            gap: 18px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.74));
            box-shadow: var(--shadow);
            padding: 18px 22px 22px;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(95deg, transparent 0%, rgba(255, 122, 89, 0.12) 45%, transparent 90%);
            pointer-events: none;
        }

        .hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            margin: 0;
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            font-weight: 700;
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .hero-sub {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            max-width: 780px;
        }

        .chip-row {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid var(--line);
            background: #fff;
        }

        .chip strong {
            color: #0f718b;
            font-weight: 800;
        }

        .layout {
            display: grid;
            grid-template-columns: 5fr 7fr;
            gap: 18px;
            align-items: start;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .panel {
            border: 1px solid rgba(255, 255, 255, 0.84);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.93);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(243, 248, 249, 0.8));
        }

        .panel-title {
            margin: 0;
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .panel-sub {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .panel-body {
            padding: 14px 16px 16px;
            display: grid;
            gap: 12px;
        }

        .auth-grid {
            display: grid;
            gap: 10px;
        }

        .row {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            align-items: end;
        }

        .field {
            display: grid;
            gap: 4px;
        }

        .field label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        input, select {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 12px;
            height: 40px;
            padding: 0 12px;
            color: var(--ink);
            font: inherit;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0f718b;
            box-shadow: 0 0 0 3px rgba(15, 113, 139, 0.12);
        }

        .btn {
            border: none;
            border-radius: 12px;
            height: 40px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            transition: transform 120ms ease, filter 120ms ease, opacity 120ms ease;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.58;
        }

        .btn-primary {
            background: linear-gradient(135deg, #126f85, #0a5565);
            color: #fff;
        }

        .btn-accent {
            background: linear-gradient(135deg, #ff8e6f, #f2643e);
            color: #fff;
        }

        .btn-ok {
            background: linear-gradient(135deg, #2cb681, #1e9f6e);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e3574c, #ce3e32);
            color: #fff;
        }

        .btn-muted {
            background: #f0f6f8;
            color: #24414b;
            border: 1px solid var(--line);
        }

        .button-line {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .meta {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
        }

        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: auto;
            max-height: 460px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th {
            text-align: left;
            font-size: 12px;
            color: #35505a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background: #ecf4f7;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #edf2f4;
            vertical-align: top;
        }

        td {
            font-size: 13px;
        }

        tr:hover td {
            background: #fbfdfe;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            height: 24px;
            border-radius: 999px;
            padding: 0 8px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #d4e2e8;
            background: #f5fafc;
            color: #2b4953;
        }

        .tag.ok {
            border-color: #b7ebd5;
            background: #e9faf2;
            color: #166b48;
        }

        .tag.warn {
            border-color: #ffe2b5;
            background: #fff7ea;
            color: #8d5a0f;
        }

        .tag.danger {
            border-color: #ffd0cb;
            background: #fff0ee;
            color: #8f261f;
        }

        .pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 8px;
            background: #f9fcfd;
        }

        .pager-info {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 12px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 220px;
        }

        .card-head {
            border-bottom: 1px solid #edf3f6;
            padding: 10px 12px;
            display: grid;
            gap: 6px;
        }

        .card-title {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.2;
        }

        .card-body {
            padding: 12px;
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .media-grid {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        }

        .thumb {
            width: 100%;
            max-width: 90px;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #d8e5ea;
            background: #f2f7f9;
        }

        .answer {
            border: 1px solid #e7eff2;
            border-radius: 10px;
            padding: 8px;
            display: grid;
            gap: 6px;
            background: #fbfdfe;
        }

        .card-foot {
            border-top: 1px solid #edf3f6;
            padding: 10px 12px;
            display: flex;
            gap: 8px;
        }

        .empty {
            border: 1px dashed #cadce4;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            background: #f8fbfc;
        }

        .loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #0f718b;
            font-size: 13px;
            font-weight: 700;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: currentColor;
            animation: pulse 1s infinite ease-in-out;
        }

        @keyframes pulse {
            0% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.1); }
            100% { opacity: 0.2; transform: scale(0.8); }
        }

        .toast-stack {
            position: fixed;
            right: 14px;
            top: 14px;
            display: grid;
            gap: 8px;
            z-index: 2000;
            width: min(360px, calc(100% - 24px));
        }

        .toast {
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: 0 8px 26px rgba(12, 35, 41, 0.16);
        }

        .toast.success { border-color: #b7ebd5; color: #166b48; background: #ecfbf4; }
        .toast.error { border-color: #ffd0cb; color: #8f261f; background: #fff1ef; }
        .toast.info { border-color: #bfe8ff; color: #0b4f72; background: #eff8ff; }

        .hide {
            display: none !important;
        }

        @media (max-width: 1120px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .shell {
                width: calc(100% - 18px);
                margin-top: 10px;
            }

            .hero {
                border-radius: 16px;
                padding: 14px;
            }

            .panel {
                border-radius: 14px;
            }

            .panel-head, .panel-body {
                padding: 12px;
            }

            .row {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .card-foot {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1 class="hero-title">Raiida Question Studio</h1>
                    <p class="hero-sub">
                        API-first admin interface for generating, checking, publishing, and reviewing questions.
                        All lists are paginated and share one design system.
                    </p>
                </div>
                <div class="chip-row">
                    <span class="chip">API Prefix <strong>/api</strong></span>
                    <span class="chip">Queue <strong>database</strong></span>
                    <span class="chip">Security <strong>Admin + Audit</strong></span>
                </div>
            </div>
        </section>

        <div class="layout">
            <div class="stack">
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">Session</h2>
                            <p class="panel-sub">Authenticate with admin credentials.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <form id="login-form" class="auth-grid">
                            <div class="field">
                                <label for="login-email">Email</label>
                                <input id="login-email" type="email" value="admin@seeder.local" required>
                            </div>
                            <div class="field">
                                <label for="login-password">Password</label>
                                <input id="login-password" type="password" value="Secret123!" required>
                            </div>
                            <div class="field">
                                <label for="login-device">Device Name</label>
                                <input id="login-device" type="text" value="question-studio-web">
                            </div>
                            <div class="button-line">
                                <button class="btn btn-primary" type="submit" id="login-btn">Sign In</button>
                                <button class="btn btn-muted hide" type="button" id="logout-btn">Sign Out</button>
                            </div>
                        </form>
                        <div class="meta" id="auth-status">No active token.</div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">Vocabulary Assets</h2>
                            <p class="panel-sub">Pick the item and generate question candidates.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="field" style="grid-column: span 2;">
                                <label for="filter-grade">Grade</label>
                                <select id="filter-grade"></select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label for="filter-period">Period</label>
                                <select id="filter-period"></select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label for="filter-week">Week</label>
                                <select id="filter-week"></select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label for="asset-per-page">Per Page</label>
                                <select id="asset-per-page">
                                    <option value="8">8</option>
                                    <option value="12" selected>12</option>
                                    <option value="20">20</option>
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 4;">
                                <button id="load-assets-btn" class="btn btn-primary" type="button">Load Assets</button>
                            </div>
                        </div>
                        <div id="asset-loading" class="loading hide"><span class="dot"></span>Loading assets…</div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Word</th>
                                        <th>Concept</th>
                                        <th>Type</th>
                                        <th>Media</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assets-tbody">
                                    <tr><td colspan="6" class="meta">Sign in to load assets.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pager">
                            <div class="pager-info" id="assets-pager-info">Page 1</div>
                            <div class="button-line">
                                <button class="btn btn-muted" type="button" id="assets-prev-btn">Prev</button>
                                <button class="btn btn-muted" type="button" id="assets-next-btn">Next</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="stack">
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">Generated Questions</h2>
                            <p class="panel-sub" id="question-selection-meta">No asset selected.</p>
                        </div>
                        <div class="button-line">
                            <button class="btn btn-accent" id="check-duplicates-btn" type="button" disabled>Recheck Duplicates</button>
                            <button class="btn btn-primary" id="batch-publish-btn" type="button" disabled>Batch Generate + Publish</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="question-loading" class="loading hide"><span class="dot"></span>Building preview…</div>
                        <div id="question-empty" class="empty">Generate questions from an asset row to start.</div>
                        <div id="question-cards" class="cards"></div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">Publish Attempts</h2>
                            <p class="panel-sub">Track statuses and clean up local attempts.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="field" style="grid-column: span 4;">
                                <label for="attempt-status-filter">Status</label>
                                <select id="attempt-status-filter">
                                    <option value="">All</option>
                                    <option value="published">Published</option>
                                    <option value="unaccepted">Unaccepted</option>
                                    <option value="failed">Failed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label for="attempt-per-page">Per Page</label>
                                <select id="attempt-per-page">
                                    <option value="8">8</option>
                                    <option value="12" selected>12</option>
                                    <option value="20">20</option>
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 6;">
                                <button class="btn btn-primary" id="refresh-attempts-btn" type="button">Refresh Attempts</button>
                            </div>
                        </div>
                        <div id="attempts-loading" class="loading hide"><span class="dot"></span>Loading attempts…</div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Concept</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Revizy ID</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attempts-tbody">
                                    <tr><td colspan="7" class="meta">Sign in to load attempts.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pager">
                            <div class="pager-info" id="attempts-pager-info">Page 1</div>
                            <div class="button-line">
                                <button class="btn btn-muted" type="button" id="attempts-prev-btn">Prev</button>
                                <button class="btn btn-muted" type="button" id="attempts-next-btn">Next</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="toast-stack" id="toast-stack"></div>

    <script>
        (() => {
            const API_BASE = '/api';
            const STORAGE_KEY = 'raiida.question-studio.auth';

            const state = {
                token: '',
                user: null,
                assets: { items: [], total: 0, page: 1, perPage: 12, grade: 'N1', period: 'P1', week: 'SEM1' },
                questions: { asset: null, items: [], duplicates: new Map(), mediaCache: new Map() },
                attempts: { items: [], page: 1, perPage: 12, status: '' }
            };

            const refs = {
                loginForm: document.getElementById('login-form'),
                loginBtn: document.getElementById('login-btn'),
                logoutBtn: document.getElementById('logout-btn'),
                authStatus: document.getElementById('auth-status'),
                loginEmail: document.getElementById('login-email'),
                loginPassword: document.getElementById('login-password'),
                loginDevice: document.getElementById('login-device'),

                filterGrade: document.getElementById('filter-grade'),
                filterPeriod: document.getElementById('filter-period'),
                filterWeek: document.getElementById('filter-week'),
                assetPerPage: document.getElementById('asset-per-page'),
                loadAssetsBtn: document.getElementById('load-assets-btn'),
                assetLoading: document.getElementById('asset-loading'),
                assetsTbody: document.getElementById('assets-tbody'),
                assetsPrevBtn: document.getElementById('assets-prev-btn'),
                assetsNextBtn: document.getElementById('assets-next-btn'),
                assetsPagerInfo: document.getElementById('assets-pager-info'),

                questionSelectionMeta: document.getElementById('question-selection-meta'),
                questionLoading: document.getElementById('question-loading'),
                questionEmpty: document.getElementById('question-empty'),
                questionCards: document.getElementById('question-cards'),
                checkDuplicatesBtn: document.getElementById('check-duplicates-btn'),
                batchPublishBtn: document.getElementById('batch-publish-btn'),

                attemptStatusFilter: document.getElementById('attempt-status-filter'),
                attemptPerPage: document.getElementById('attempt-per-page'),
                refreshAttemptsBtn: document.getElementById('refresh-attempts-btn'),
                attemptsLoading: document.getElementById('attempts-loading'),
                attemptsTbody: document.getElementById('attempts-tbody'),
                attemptsPrevBtn: document.getElementById('attempts-prev-btn'),
                attemptsNextBtn: document.getElementById('attempts-next-btn'),
                attemptsPagerInfo: document.getElementById('attempts-pager-info'),

                toastStack: document.getElementById('toast-stack')
            };

            function boot() {
                hydrateFilterSelects();
                bindEvents();
                restoreSession();
            }

            function bindEvents() {
                refs.loginForm.addEventListener('submit', onLoginSubmit);
                refs.logoutBtn.addEventListener('click', onLogout);

                refs.loadAssetsBtn.addEventListener('click', () => {
                    state.assets.page = 1;
                    loadAssets();
                });

                refs.assetPerPage.addEventListener('change', () => {
                    state.assets.perPage = Number(refs.assetPerPage.value || 12);
                    state.assets.page = 1;
                    loadAssets();
                });

                refs.filterGrade.addEventListener('change', () => {
                    state.assets.grade = refs.filterGrade.value;
                });
                refs.filterPeriod.addEventListener('change', () => {
                    state.assets.period = refs.filterPeriod.value;
                });
                refs.filterWeek.addEventListener('change', () => {
                    state.assets.week = refs.filterWeek.value;
                });

                refs.assetsPrevBtn.addEventListener('click', () => {
                    if (state.assets.page <= 1) return;
                    state.assets.page -= 1;
                    loadAssets();
                });

                refs.assetsNextBtn.addEventListener('click', () => {
                    const totalPages = Math.max(1, Math.ceil(state.assets.total / state.assets.perPage));
                    if (state.assets.page >= totalPages) return;
                    state.assets.page += 1;
                    loadAssets();
                });

                refs.checkDuplicatesBtn.addEventListener('click', async () => {
                    await refreshDuplicates();
                    renderQuestionCards();
                    toast('Duplicate check refreshed.', 'info');
                });

                refs.batchPublishBtn.addEventListener('click', onBatchGeneratePublish);

                refs.refreshAttemptsBtn.addEventListener('click', () => {
                    state.attempts.page = 1;
                    loadAttempts();
                });

                refs.attemptStatusFilter.addEventListener('change', () => {
                    state.attempts.status = refs.attemptStatusFilter.value;
                    state.attempts.page = 1;
                    loadAttempts();
                });

                refs.attemptPerPage.addEventListener('change', () => {
                    state.attempts.perPage = Number(refs.attemptPerPage.value || 12);
                    state.attempts.page = 1;
                    renderAttemptsTable();
                });

                refs.attemptsPrevBtn.addEventListener('click', () => {
                    if (state.attempts.page <= 1) return;
                    state.attempts.page -= 1;
                    renderAttemptsTable();
                });

                refs.attemptsNextBtn.addEventListener('click', () => {
                    const totalPages = Math.max(1, Math.ceil(state.attempts.items.length / state.attempts.perPage));
                    if (state.attempts.page >= totalPages) return;
                    state.attempts.page += 1;
                    renderAttemptsTable();
                });
            }

            function hydrateFilterSelects() {
                refs.filterGrade.innerHTML = makeOptions(['N1', 'N2', 'N3', 'N4', 'N5', 'N6'], state.assets.grade);
                refs.filterPeriod.innerHTML = makeOptions(['P1', 'P2', 'P3', 'P4'], state.assets.period);
                refs.filterWeek.innerHTML = makeOptions(['SEM1', 'SEM2', 'SEM3', 'SEM4'], state.assets.week);
            }

            function makeOptions(values, selected) {
                return values.map((value) => {
                    const active = value === selected ? 'selected' : '';
                    return `<option value="${escapeHtml(value)}" ${active}>${escapeHtml(value)}</option>`;
                }).join('');
            }

            async function restoreSession() {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) {
                    updateAuthUI();
                    return;
                }

                try {
                    const parsed = JSON.parse(raw);
                    state.token = typeof parsed.token === 'string' ? parsed.token : '';
                } catch (_) {
                    state.token = '';
                }

                if (!state.token) {
                    updateAuthUI();
                    return;
                }

                try {
                    const me = await apiFetch('/auth/me');
                    state.user = me;
                    toast(`Session restored for ${me.email}.`, 'info');
                } catch (_) {
                    clearSession();
                }

                updateAuthUI();
                if (state.user) {
                    await Promise.all([loadAssets(), loadAttempts()]);
                }
            }

            async function onLoginSubmit(event) {
                event.preventDefault();

                setBusy(refs.loginBtn, true, 'Signing In...');
                try {
                    const payload = {
                        email: refs.loginEmail.value.trim(),
                        password: refs.loginPassword.value,
                        device_name: refs.loginDevice.value.trim() || 'question-studio-web'
                    };

                    const result = await apiFetch('/auth/login', { method: 'POST', body: payload });
                    state.token = result.token;
                    state.user = result.user;
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({ token: state.token }));

                    updateAuthUI();
                    toast(`Welcome ${state.user.name} (${state.user.role}).`, 'success');

                    await Promise.all([loadAssets(), loadAttempts()]);
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    setBusy(refs.loginBtn, false, 'Sign In');
                }
            }

            async function onLogout() {
                if (state.token) {
                    try {
                        await apiFetch('/auth/logout', { method: 'POST' });
                    } catch (_) {
                    }
                }

                clearSession();
                updateAuthUI();
                renderAssetsTable();
                renderQuestionCards();
                renderAttemptsTable();
                toast('Signed out.', 'info');
            }

            function clearSession() {
                state.token = '';
                state.user = null;
                state.assets.items = [];
                state.assets.total = 0;
                state.questions.asset = null;
                state.questions.items = [];
                state.questions.duplicates = new Map();
                state.questions.mediaCache = new Map();
                state.attempts.items = [];
                localStorage.removeItem(STORAGE_KEY);
            }

            function updateAuthUI() {
                const active = Boolean(state.user && state.token);
                refs.loginBtn.classList.toggle('hide', active);
                refs.logoutBtn.classList.toggle('hide', !active);
                refs.batchPublishBtn.disabled = !active || state.user.role !== 'admin';
                refs.checkDuplicatesBtn.disabled = !active || state.questions.items.length === 0;
                refs.loadAssetsBtn.disabled = !active;
                refs.refreshAttemptsBtn.disabled = !active;

                if (!active) {
                    refs.authStatus.textContent = 'No active token.';
                    return;
                }

                refs.authStatus.textContent = `Signed in as ${state.user.email} (${state.user.role})`;
            }

            async function loadAssets() {
                if (!state.token) return;

                refs.assetLoading.classList.remove('hide');
                refs.loadAssetsBtn.disabled = true;

                state.assets.grade = refs.filterGrade.value;
                state.assets.period = refs.filterPeriod.value;
                state.assets.week = refs.filterWeek.value;
                state.assets.perPage = Number(refs.assetPerPage.value || 12);

                const offset = Math.max(0, (state.assets.page - 1) * state.assets.perPage);
                const query = new URLSearchParams({
                    grade: state.assets.grade,
                    period: state.assets.period,
                    week: state.assets.week,
                    limit: String(state.assets.perPage),
                    offset: String(offset)
                });

                try {
                    const result = await apiFetch(`/vocabulary-assets?${query.toString()}`);
                    state.assets.items = Array.isArray(result.items) ? result.items : [];
                    state.assets.total = Number(result.total || 0);
                    renderAssetsTable();
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    refs.assetLoading.classList.add('hide');
                    refs.loadAssetsBtn.disabled = !state.token;
                }
            }

            function renderAssetsTable() {
                const rows = state.assets.items;
                if (!state.token) {
                    refs.assetsTbody.innerHTML = '<tr><td colspan="6" class="meta">Sign in to load assets.</td></tr>';
                    refs.assetsPagerInfo.textContent = 'Page 1';
                    return;
                }

                if (rows.length === 0) {
                    refs.assetsTbody.innerHTML = '<tr><td colspan="6" class="meta">No assets for selected filter.</td></tr>';
                } else {
                    refs.assetsTbody.innerHTML = rows.map((item) => {
                        const hasImage = Boolean(item.revizy_image_file_id);
                        const hasAudio = Boolean(item.revizy_audio_file_id);
                        const generateDisabled = !item.concept_id || !item.lexical_type || !item.revizy_image_file_id;
                        return `
                            <tr>
                                <td>${escapeHtml(String(item.id))}</td>
                                <td>
                                    <strong>${escapeHtml(item.name || item.word || '-')}</strong><br>
                                    <span class="meta">${escapeHtml(item.name_ar || item.ar_translation || '')}</span>
                                </td>
                                <td>${escapeHtml(item.concept_id || '-')}</td>
                                <td>${escapeHtml(item.lexical_type || '-')}</td>
                                <td>
                                    <span class="tag ${hasImage ? 'ok' : 'warn'}">${hasImage ? 'Image' : 'No image'}</span>
                                    <span class="tag ${hasAudio ? 'ok' : 'warn'}">${hasAudio ? 'Audio' : 'No audio'}</span>
                                </td>
                                <td>
                                    <button class="btn btn-primary generate-btn"
                                        data-id="${escapeHtml(String(item.id))}"
                                        data-name="${escapeHtml(item.name || item.word || '')}"
                                        data-concept="${escapeHtml(item.concept_id || '')}"
                                        ${generateDisabled ? 'disabled' : ''}>
                                        Generate
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                refs.assetsTbody.querySelectorAll('.generate-btn').forEach((button) => {
                    button.addEventListener('click', () => {
                        const id = Number(button.dataset.id || 0);
                        const name = button.dataset.name || '';
                        const concept = button.dataset.concept || '';
                        generateForAsset({ id, name, concept });
                    });
                });

                const totalPages = Math.max(1, Math.ceil(state.assets.total / state.assets.perPage));
                refs.assetsPrevBtn.disabled = state.assets.page <= 1;
                refs.assetsNextBtn.disabled = state.assets.page >= totalPages;
                refs.assetsPagerInfo.textContent = `Page ${state.assets.page} / ${totalPages} · Total ${state.assets.total}`;
            }

            async function generateForAsset(asset) {
                refs.questionLoading.classList.remove('hide');
                refs.questionEmpty.classList.add('hide');
                refs.questionCards.innerHTML = '';
                refs.questionSelectionMeta.textContent = `Generating for asset #${asset.id} (${asset.name || 'unnamed'})...`;

                try {
                    const questions = await apiFetch(`/generate-questions/${asset.id}`);
                    state.questions.asset = asset;
                    state.questions.items = Array.isArray(questions) ? questions : [];
                    state.questions.duplicates = new Map();
                    state.questions.mediaCache = new Map();

                    await hydrateQuestionMetadata();

                    refs.checkDuplicatesBtn.disabled = state.questions.items.length === 0;
                    refs.questionSelectionMeta.textContent =
                        `Asset #${asset.id} · Concept ${asset.concept || 'unknown'} · ${state.questions.items.length} generated`;

                    renderQuestionCards();
                    toast(`Generated ${state.questions.items.length} question(s).`, 'success');
                } catch (error) {
                    refs.questionSelectionMeta.textContent = 'Generation failed.';
                    state.questions.items = [];
                    renderQuestionCards();
                    toast(error.message, 'error');
                } finally {
                    refs.questionLoading.classList.add('hide');
                }
            }

            async function hydrateQuestionMetadata() {
                if (state.questions.items.length === 0) return;

                await refreshDuplicates();

                const secretIds = collectSecretIds(state.questions.items);
                const uncached = secretIds.filter((id) => !state.questions.mediaCache.has(id));

                await Promise.all(uncached.map(async (secretId) => {
                    try {
                        const asset = await apiFetch(`/vocabulary-assets/by-secret-id/${encodeURIComponent(secretId)}`);
                        state.questions.mediaCache.set(secretId, asset);
                    } catch (_) {
                        state.questions.mediaCache.set(secretId, null);
                    }
                }));
            }

            async function refreshDuplicates() {
                if (state.questions.items.length === 0) {
                    state.questions.duplicates = new Map();
                    return;
                }

                const payload = state.questions.items.map((question, index) => ({
                    index,
                    concept_id: String(question.concept_id || ''),
                    data: isObject(question.data) ? question.data : {}
                }));

                const result = await apiFetch('/questions/check-duplicates', {
                    method: 'POST',
                    body: { questions: payload }
                });

                state.questions.duplicates = new Map();
                const duplicates = Array.isArray(result.duplicates) ? result.duplicates : [];
                duplicates.forEach((row) => {
                    state.questions.duplicates.set(Number(row.index), row);
                });
            }

            function renderQuestionCards() {
                const questions = state.questions.items;
                refs.questionCards.innerHTML = '';

                if (!state.token) {
                    refs.questionEmpty.classList.remove('hide');
                    refs.questionEmpty.textContent = 'Sign in first.';
                    refs.checkDuplicatesBtn.disabled = true;
                    return;
                }

                if (questions.length === 0) {
                    refs.questionEmpty.classList.remove('hide');
                    refs.questionEmpty.textContent = 'Generate questions from an asset row to start.';
                    refs.checkDuplicatesBtn.disabled = true;
                    return;
                }

                refs.questionEmpty.classList.add('hide');
                refs.checkDuplicatesBtn.disabled = false;

                const fragment = document.createDocumentFragment();
                questions.forEach((question, index) => {
                    const card = document.createElement('article');
                    card.className = 'card';
                    card.innerHTML = renderQuestionCard(question, index);
                    fragment.appendChild(card);
                });

                refs.questionCards.appendChild(fragment);

                refs.questionCards.querySelectorAll('.publish-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const index = Number(button.dataset.index);
                        await publishQuestion(index, button);
                    });
                });

                refs.questionCards.querySelectorAll('.unaccept-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const index = Number(button.dataset.index);
                        await unacceptQuestion(index, button);
                    });
                });
            }

            function renderQuestionCard(question, index) {
                const duplicate = state.questions.duplicates.get(index);
                const data = isObject(question.data) ? question.data : {};
                const answers = Array.isArray(data.answers) ? data.answers : [];
                const questionMedia = renderMediaForSecretMap(data.media);

                const answersHtml = answers.slice(0, 8).map((answer) => {
                    const answerMedia = renderMediaForSecretMap(answer.media);
                    const statusClass = answer.is_correct ? 'ok' : 'warn';
                    const statusText = answer.is_correct ? 'Correct' : 'Distractor';

                    return `
                        <div class="answer">
                            <div>
                                <span class="tag ${statusClass}">${statusText}</span>
                                <span class="meta">${escapeHtml(String(answer.body ?? ''))}</span>
                            </div>
                            ${answerMedia}
                        </div>
                    `;
                }).join('');

                const duplicateBadge = duplicate
                    ? `<span class="tag ok">Published${duplicate.revizy_question_id ? ' · ' + escapeHtml(String(duplicate.revizy_question_id)) : ''}</span>`
                    : '<span class="tag">Not published</span>';

                return `
                    <div class="card-head">
                        <div class="button-line" style="justify-content: space-between;">
                            <h3 class="card-title">${escapeHtml(String(question.name ?? 'Untitled question'))}</h3>
                            ${duplicateBadge}
                        </div>
                        <div class="meta">#${index} · type ${escapeHtml(String(question.type ?? '-'))} · concept ${escapeHtml(String(question.concept_id ?? '-'))}</div>
                    </div>
                    <div class="card-body">
                        <div class="meta"><strong>Instruction:</strong> ${escapeHtml(String(data.instruction ?? '-'))}</div>
                        <div class="meta"><strong>Body:</strong> ${escapeHtml(String(data.body ?? '-'))}</div>
                        ${questionMedia}
                        <div class="meta"><strong>Answers (${answers.length})</strong></div>
                        <div class="media-grid">${answersHtml || '<span class="meta">No answers.</span>'}</div>
                    </div>
                    <div class="card-foot">
                        <button class="btn btn-ok publish-btn" data-index="${index}">Publish</button>
                        <button class="btn btn-danger unaccept-btn" data-index="${index}">Unaccept</button>
                    </div>
                `;
            }

            function renderMediaForSecretMap(mediaRef) {
                if (!isObject(mediaRef)) return '<div class="meta">No media</div>';

                const imageId = typeof mediaRef.image === 'string' ? mediaRef.image : '';
                const audioId = typeof mediaRef.audio === 'string' ? mediaRef.audio : '';
                const imageAsset = imageId ? state.questions.mediaCache.get(imageId) : null;
                const audioAsset = audioId ? state.questions.mediaCache.get(audioId) : null;

                const imagePath = imageAsset && imageAsset.image ? normalizeImagePath(imageAsset.image) : '';
                const audioPath = audioAsset && audioAsset.audio ? normalizeAudioPath(audioAsset.audio) : '';

                if (!imagePath && !audioPath) {
                    return '<div class="meta">No resolvable media</div>';
                }

                return `
                    <div class="media-grid">
                        ${imagePath ? `<img class="thumb" src="${escapeHtml(imagePath)}" alt="image">` : ''}
                        ${audioPath ? `<audio controls preload="none" src="${escapeHtml(audioPath)}"></audio>` : ''}
                    </div>
                `;
            }

            async function publishQuestion(index, button) {
                const question = state.questions.items[index];
                if (!question) return;

                setBusy(button, true, 'Publishing...');
                try {
                    const payload = {
                        local_question_id: index,
                        concept_id: String(question.concept_id || ''),
                        name: String(question.name || 'Question'),
                        type: String(question.type || 'universal'),
                        status: 'published',
                        data: isObject(question.data) ? question.data : {}
                    };

                    const result = await apiFetch(`/questions/${index}/publish`, {
                        method: 'POST',
                        body: payload
                    });

                    await Promise.all([refreshDuplicates(), loadAttempts()]);
                    renderQuestionCards();

                    toast(`Published${result.revizy_question_id ? ` (${result.revizy_question_id})` : ''}.`, 'success');
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    setBusy(button, false, 'Publish');
                }
            }

            async function unacceptQuestion(index, button) {
                const question = state.questions.items[index];
                if (!question) return;

                setBusy(button, true, 'Unaccepting...');
                try {
                    await apiFetch(`/questions/${index}/unaccept`, {
                        method: 'POST',
                        body: {
                            local_question_id: index,
                            concept_id: String(question.concept_id || ''),
                            name: String(question.name || 'Question'),
                            data: isObject(question.data) ? question.data : {}
                        }
                    });

                    await loadAttempts();
                    toast('Question marked as unaccepted.', 'success');
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    setBusy(button, false, 'Unaccept');
                }
            }

            async function onBatchGeneratePublish() {
                if (!state.user || state.user.role !== 'admin') {
                    toast('Batch action is admin-only.', 'error');
                    return;
                }

                setBusy(refs.batchPublishBtn, true, 'Running Batch...');
                try {
                    const result = await apiFetch('/batch-generate-publish', {
                        method: 'POST',
                        body: {}
                    }, true);

                    await loadAttempts();
                    toast(`Batch complete: total ${result.total}, published ${result.published}, failed ${result.failed}.`, 'success');
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    setBusy(refs.batchPublishBtn, false, 'Batch Generate + Publish');
                }
            }

            async function loadAttempts() {
                if (!state.token) return;

                refs.attemptsLoading.classList.remove('hide');
                refs.refreshAttemptsBtn.disabled = true;

                try {
                    const status = refs.attemptStatusFilter.value || '';
                    state.attempts.status = status;
                    const query = status ? `?status=${encodeURIComponent(status)}` : '';
                    const attempts = await apiFetch(`/questions/publish-attempts${query}`);
                    state.attempts.items = Array.isArray(attempts) ? attempts : [];
                    state.attempts.page = 1;
                    renderAttemptsTable();
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    refs.attemptsLoading.classList.add('hide');
                    refs.refreshAttemptsBtn.disabled = !state.token;
                }
            }

            function renderAttemptsTable() {
                const list = state.attempts.items;
                if (!state.token) {
                    refs.attemptsTbody.innerHTML = '<tr><td colspan="7" class="meta">Sign in to load attempts.</td></tr>';
                    refs.attemptsPagerInfo.textContent = 'Page 1';
                    return;
                }

                const perPage = Number(refs.attemptPerPage.value || state.attempts.perPage || 12);
                state.attempts.perPage = perPage;
                const totalPages = Math.max(1, Math.ceil(list.length / perPage));
                if (state.attempts.page > totalPages) {
                    state.attempts.page = totalPages;
                }

                const start = (state.attempts.page - 1) * perPage;
                const pageItems = list.slice(start, start + perPage);

                if (pageItems.length === 0) {
                    refs.attemptsTbody.innerHTML = '<tr><td colspan="7" class="meta">No attempts for current filter.</td></tr>';
                } else {
                    refs.attemptsTbody.innerHTML = pageItems.map((attempt) => {
                        return `
                            <tr>
                                <td>${escapeHtml(String(attempt.id ?? '-'))}</td>
                                <td>${escapeHtml(String(attempt.concept_id ?? '-'))}</td>
                                <td>${escapeHtml(String(attempt.name ?? '-'))}</td>
                                <td><span class="tag ${statusClass(attempt.status)}">${escapeHtml(String(attempt.status ?? 'unknown'))}</span></td>
                                <td>${escapeHtml(String(attempt.revizy_question_id ?? '-'))}</td>
                                <td>${escapeHtml(formatDateTime(attempt.created_at))}</td>
                                <td><button class="btn btn-muted delete-attempt-btn" data-id="${escapeHtml(String(attempt.id ?? ''))}">Delete</button></td>
                            </tr>
                        `;
                    }).join('');
                }

                refs.attemptsTbody.querySelectorAll('.delete-attempt-btn').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const id = Number(button.dataset.id);
                        if (!id) return;
                        await deleteAttempt(id, button);
                    });
                });

                refs.attemptsPrevBtn.disabled = state.attempts.page <= 1;
                refs.attemptsNextBtn.disabled = state.attempts.page >= totalPages;
                refs.attemptsPagerInfo.textContent = `Page ${state.attempts.page} / ${totalPages} · Total ${list.length}`;
            }

            async function deleteAttempt(id, button) {
                setBusy(button, true, 'Deleting...');
                try {
                    await apiFetch(`/questions/${id}`, { method: 'DELETE' });
                    await loadAttempts();
                    toast(`Deleted attempt #${id}.`, 'info');
                } catch (error) {
                    toast(error.message, 'error');
                } finally {
                    setBusy(button, false, 'Delete');
                }
            }

            function statusClass(status) {
                if (status === 'published') return 'ok';
                if (status === 'failed' || status === 'unaccepted') return 'danger';
                return 'warn';
            }

            function collectSecretIds(questions) {
                const ids = new Set();
                questions.forEach((question) => {
                    const data = isObject(question.data) ? question.data : {};
                    extractFromMedia(data.media, ids);
                    if (Array.isArray(data.answers)) {
                        data.answers.forEach((answer) => {
                            if (isObject(answer)) {
                                extractFromMedia(answer.media, ids);
                            }
                        });
                    }
                });
                return Array.from(ids);
            }

            function extractFromMedia(media, ids) {
                if (!isObject(media)) return;
                if (typeof media.image === 'string' && media.image.trim() !== '') ids.add(media.image);
                if (typeof media.audio === 'string' && media.audio.trim() !== '') ids.add(media.audio);
            }

            async function apiFetch(path, options = {}, includeContext = false) {
                const headers = {
                    'Accept': 'application/json'
                };

                if (state.token) {
                    headers['Authorization'] = `Bearer ${state.token}`;
                }

                const requestOptions = {
                    method: options.method || 'GET',
                    headers
                };

                if (options.body !== undefined) {
                    headers['Content-Type'] = 'application/json';
                    requestOptions.body = JSON.stringify(options.body);
                }

                if (includeContext || (requestOptions.method !== 'GET' && requestOptions.method !== 'HEAD')) {
                    headers['X-Workflow-Context-Id'] = createContextId();
                }

                const response = await fetch(`${API_BASE}${path}`, requestOptions);
                const text = await response.text();

                let payload;
                try {
                    payload = text ? JSON.parse(text) : {};
                } catch (_) {
                    payload = {};
                }

                if (!response.ok) {
                    throw new Error(normalizeError(payload, response.status, response.statusText));
                }

                return payload;
            }

            function normalizeError(payload, status, statusText) {
                if (payload && typeof payload.detail === 'string' && payload.detail.trim() !== '') {
                    return payload.detail;
                }
                if (payload && typeof payload.message === 'string' && payload.message.trim() !== '') {
                    return payload.message;
                }
                return `Request failed (${status} ${statusText}).`;
            }

            function createContextId() {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return `web-${window.crypto.randomUUID()}`;
                }
                return `web-${Date.now()}-${Math.random().toString(16).slice(2)}`;
            }

            function toast(message, type = 'info') {
                const node = document.createElement('div');
                node.className = `toast ${type}`;
                node.textContent = message;
                refs.toastStack.appendChild(node);

                window.setTimeout(() => {
                    node.remove();
                }, 3600);
            }

            function setBusy(button, busy, label) {
                if (!(button instanceof HTMLElement)) return;
                button.disabled = busy;
                if (typeof label === 'string') {
                    button.textContent = label;
                }
            }

            function normalizeImagePath(path) {
                if (!path) return '';
                if (/^https?:\/\//i.test(path)) return path;
                if (path.startsWith('/')) return path;
                return `/${path}`;
            }

            function normalizeAudioPath(path) {
                if (!path) return '';
                if (/^https?:\/\//i.test(path)) return path;
                if (path.startsWith('/')) return path;
                return `/audios/${path}`;
            }

            function formatDateTime(value) {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return String(value);
                return date.toLocaleString();
            }

            function isObject(value) {
                return value !== null && typeof value === 'object' && !Array.isArray(value);
            }

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#39;');
            }

            boot();
        })();
    </script>
</body>
</html>
