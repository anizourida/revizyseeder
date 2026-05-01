<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Enseignant') — Revizy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ─── CSS Reset & Variables ─────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #2E76B6;
            --primary-dark: #245d94;
            --primary-light: #e8f1f8;
            --accent: #00AAA4;
            --accent-dark: #008a85;
            --pink: #DC03A2;
            --red: #AF0A54;
            --bg: #f0f4f8;
            --surface: #ffffff;
            --text: #1a1a2e;
            --text-muted: #6b7280;
            --border: #e2e8f0;
            --success-bg: #d1fae5;
            --success-text: #065f46;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 40px rgba(0, 0, 0, 0.1);
            --transition: 0.2s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ─── Navigation ───────────────────────────────────────── */
        .nav {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            padding: 0 24px;
            box-shadow: 0 2px 12px rgba(46, 118, 182, 0.25);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }

        .nav-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all var(--transition);
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }

        .nav-logout {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            font-family: inherit;
            transition: all var(--transition);
        }

        .nav-logout:hover {
            background: rgba(255,255,255,0.2);
        }

        /* ─── Mobile nav toggle ────────────────────────────────── */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
        }

        .nav-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            margin: 5px 0;
            border-radius: 2px;
            transition: all var(--transition);
        }

        /* ─── Main Content ─────────────────────────────────────── */
        .main {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        /* ─── Auth Layout (centered card) ──────────────────────── */
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--bg) 50%, #e0f7f6 100%);
        }

        .auth-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 40px;
            width: 100%;
            max-width: 440px;
        }

        .auth-card-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-card-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .auth-card-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .auth-card-logo {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        /* ─── Cards ────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title .icon {
            width: 20px;
            height: 20px;
        }

        /* ─── Form Elements ────────────────────────────────────── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.9375rem;
            font-family: inherit;
            color: var(--text);
            background: var(--surface);
            transition: all var(--transition);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 118, 182, 0.12);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        .form-input.is-invalid {
            border-color: var(--red);
        }

        .form-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ─── Buttons ──────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: none;
            transition: all var(--transition);
            text-decoration: none;
            line-height: 1.4;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 2px 8px rgba(46, 118, 182, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(46, 118, 182, 0.4);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 2px 8px rgba(0, 170, 164, 0.3);
        }

        .btn-accent:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0, 170, 164, 0.4);
        }

        .btn-danger {
            background: transparent;
            color: var(--red);
            border: 1px solid var(--red);
            padding: 8px 16px;
            font-size: 0.8125rem;
        }

        .btn-danger:hover {
            background: var(--red);
            color: #fff;
        }

        .btn-ghost {
            background: transparent;
            color: var(--primary);
            padding: 8px 16px;
        }

        .btn-ghost:hover {
            background: var(--primary-light);
        }

        .btn-block {
            width: 100%;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.8125rem;
        }

        /* ─── Alerts ───────────────────────────────────────────── */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
        }

        .alert-warning {
            background: var(--warning-bg);
            color: var(--warning-text);
        }

        /* ─── Validation Errors ────────────────────────────────── */
        .validation-error {
            color: var(--red);
            font-size: 0.8125rem;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ─── Table ────────────────────────────────────────────── */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: 12px 16px;
            border-bottom: 2px solid var(--border);
        }

        .table td {
            padding: 14px 16px;
            font-size: 0.9375rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background: var(--primary-light);
        }

        .table-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .table-empty svg {
            width: 48px;
            height: 48px;
            opacity: 0.3;
            margin-bottom: 12px;
        }

        /* ─── Auth Links ───────────────────────────────────────── */
        .auth-links {
            text-align: center;
            margin-top: 24px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .auth-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        /* ─── Checkbox ─────────────────────────────────────────── */
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .form-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .form-checkbox span {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* ─── Badge ────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-count {
            background: var(--primary-light);
            color: var(--primary);
        }

        /* ─── Stats row ────────────────────────────────────────── */
        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            flex: 1;
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card .stat-label {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ─── Page header ──────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* ─── Responsive ───────────────────────────────────────── */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, var(--primary), var(--accent));
                flex-direction: column;
                padding: 12px;
                gap: 4px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }

            .nav-links.open {
                display: flex;
            }

            .nav-toggle {
                display: block;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .auth-card {
                padding: 28px 20px;
            }

            .stats-row {
                flex-direction: column;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

        /* ─── Divider ──────────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        /* ─── Utility ──────────────────────────────────────────── */
        .text-center { text-align: center; }
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 0.875rem; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mb-4 { margin-bottom: 16px; }
    </style>
    @yield('head')
</head>
<body>
    @auth('teacher')
    <nav class="nav">
        <div class="nav-inner">
            <a href="{{ route('teacher.students.index') }}" class="nav-brand">Revizy Enseignants</a>
            <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links">
                <li>
                    <a href="{{ route('teacher.students.index') }}"
                       class="{{ request()->routeIs('teacher.students.*') ? 'active' : '' }}">
                        Mes élèves
                    </a>
                </li>
                <li>
                    <a href="{{ route('teacher.profile.show') }}"
                       class="{{ request()->routeIs('teacher.profile.*') ? 'active' : '' }}">
                        Mon profil
                    </a>
                </li>
                <li>
                    <form action="{{ route('teacher.logout') }}" method="POST" style="margin:0">
                        @csrf
                        <button type="submit" class="nav-logout">Déconnexion</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
    @endauth

    @if(session('success'))
        <div style="max-width:900px;margin:16px auto;padding:0 20px;">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div style="max-width:900px;margin:16px auto;padding:0 20px;">
            <div class="alert alert-error">{{ session('error') }}</div>
        </div>
    @endif

    @if(session('warning'))
        <div style="max-width:900px;margin:16px auto;padding:0 20px;">
            <div class="alert alert-warning">{{ session('warning') }}</div>
        </div>
    @endif

    @yield('content')

    @yield('scripts')
</body>
</html>
