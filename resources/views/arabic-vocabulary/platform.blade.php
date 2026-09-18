<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة المفردات العربية المصورة | Seeder Arabic Vocabulary Studio</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Kufi+Arabic:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-body: #0a0f1d;
            --bg-surface: #111827;
            --bg-card: rgba(17, 24, 39, 0.85);
            --bg-card-hover: rgba(31, 41, 55, 0.95);
            --border-card: rgba(255, 255, 255, 0.08);
            --border-card-hover: rgba(16, 185, 129, 0.35);
            
            --primary: #10b981;
            --primary-glow: rgba(16, 185, 129, 0.25);
            --primary-dark: #059669;
            --accent-sky: #0ea5e9;
            --accent-purple: #a855f7;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --text-dim: #6b7280;
            
            --font-arabic: 'Amiri', 'Traditional Arabic', serif;
            --font-kufi: 'Noto Kufi Arabic', sans-serif;
            --font-latin: 'Plus Jakarta Sans', sans-serif;
            
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            
            --shadow-card: 0 8px 24px -6px rgba(0, 0, 0, 0.45);
            --shadow-hover: 0 16px 36px -8px rgba(16, 185, 129, 0.18), 0 8px 16px -4px rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(16, 185, 129, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(14, 165, 233, 0.06) 0%, transparent 45%);
            color: var(--text-main);
            font-family: var(--font-kufi);
            min-height: 100vh;
            padding-bottom: 80px;
            line-height: 1.6;
        }

        /* Top Navigation Bar */
        .top-nav {
            background: rgba(10, 15, 29, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .brand-text h1 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.01em;
        }

        .brand-text span {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: var(--font-latin);
            direction: ltr;
            display: inline-block;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-main);
        }

        .btn-nav:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .btn-nav.primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: #10b981;
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .btn-nav.primary:hover {
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }

        /* Container */
        .container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            backdrop-filter: blur(12px);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-icon.emerald { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .stat-icon.sky { background: rgba(14, 165, 233, 0.15); color: #0ea5e9; }
        .stat-icon.purple { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
        .stat-icon.amber { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

        .stat-meta h4 {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .stat-meta .stat-val {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            font-family: var(--font-latin);
            line-height: 1;
        }

        /* Controls Section */
        .controls-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            backdrop-filter: blur(16px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: var(--shadow-card);
        }

        .control-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .control-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            min-width: 80px;
        }

        .pills-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.09);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            transition: all 0.2s ease;
            text-decoration: none;
            user-select: none;
        }

        .filter-pill:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .filter-pill.active {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
            box-shadow: 0 2px 10px var(--primary-glow);
        }

        .filter-pill.sky.active {
            background: #0ea5e9;
            border-color: #0ea5e9;
            box-shadow: 0 2px 10px rgba(14, 165, 233, 0.3);
        }

        .filter-pill.purple.active {
            background: #a855f7;
            border-color: #a855f7;
            box-shadow: 0 2px 10px rgba(168, 85, 247, 0.3);
        }

        .filter-pill.amber.active {
            background: #f59e0b;
            border-color: #f59e0b;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.3);
        }

        /* Search & Toggle Bar */
        .search-toggle-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: space-between;
            flex-wrap: wrap;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 280px;
        }

        .search-input-wrapper i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        .search-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 44px 10px 18px;
            border-radius: var(--radius-sm);
            color: #fff;
            font-size: 0.9rem;
            font-family: var(--font-kufi);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .toggle-switch-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: var(--radius-full);
            transition: background 0.2s ease;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s ease;
        }

        .toggle-switch.checked {
            background: var(--primary);
        }

        .toggle-switch.checked::after {
            transform: translateX(-20px);
        }

        .toggle-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Results Counter Header */
        .results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .results-count {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .results-count strong {
            color: #fff;
            font-family: var(--font-latin);
        }

        /* Vocabulary Cards Grid */
        .vocab-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 22px;
        }

        .vocab-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: var(--shadow-card);
        }

        .vocab-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-card-hover);
            box-shadow: var(--shadow-hover);
            background: var(--bg-card-hover);
        }

        /* Card Image Box */
        .card-img-box {
            position: relative;
            width: 100%;
            height: 190px;
            background: #0d1322;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
        }

        .card-img-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
            transition: transform 0.3s ease;
        }

        .vocab-card:hover .card-img-box img {
            transform: scale(1.04);
        }

        .no-img-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-dim);
            font-size: 0.8rem;
        }

        .no-img-placeholder i {
            font-size: 2.2rem;
            opacity: 0.5;
        }

        .zoom-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.65);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }

        .card-img-box:hover .zoom-overlay {
            opacity: 1;
            transform: scale(1);
        }

        /* Card Content */
        .card-content {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 12px;
        }

        .card-header-line {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .word-vocalized {
            font-family: var(--font-arabic);
            font-size: 1.85rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            letter-spacing: 0.01em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        .strategy-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .strategy-badge.emerald { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .strategy-badge.sky { background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); }
        .strategy-badge.purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .strategy-badge.amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .strategy-badge.gray { background: rgba(156, 163, 175, 0.12); color: #d1d5db; border: 1px solid rgba(156, 163, 175, 0.2); }

        .raw-word-box {
            font-size: 0.8rem;
            color: var(--text-dim);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .raw-word-box span {
            background: rgba(255, 255, 255, 0.04);
            padding: 2px 8px;
            border-radius: 4px;
            font-family: var(--font-kufi);
            color: var(--text-muted);
        }

        /* Example / Meaning / Context */
        .context-box {
            background: rgba(0, 0, 0, 0.22);
            border-right: 3px solid var(--primary);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #d1d5db;
            line-height: 1.5;
            font-family: var(--font-arabic);
        }

        .context-box.sky-border {
            border-right-color: var(--accent-sky);
        }

        .context-box.purple-border {
            border-right-color: var(--accent-purple);
        }

        .context-box.amber-border {
            border-right-color: var(--accent-amber);
        }

        .context-box small {
            display: block;
            font-size: 0.72rem;
            font-family: var(--font-kufi);
            color: var(--text-dim);
            margin-bottom: 2px;
            font-weight: 600;
        }

        /* Card Footer Meta */
        .card-footer-meta {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-dim);
            font-family: var(--font-latin);
        }

        .scope-pills {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .scope-pill {
            background: rgba(255, 255, 255, 0.06);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            color: #e5e7eb;
        }

        .lesson-code {
            color: var(--text-dim);
            font-size: 0.72rem;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            padding: 80px 20px;
            text-align: center;
            background: var(--bg-card);
            border: 1px dashed var(--border-card);
            border-radius: var(--radius-lg);
        }

        .empty-state i {
            font-size: 3.5rem;
            color: var(--text-dim);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Modal Lightbox */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(12px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .modal-backdrop.show {
            display: flex;
            opacity: 1;
        }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: var(--radius-lg);
            max-width: 720px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            transform: scale(0.95);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-backdrop.show .modal-box {
            transform: scale(1);
        }

        .modal-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            font-family: var(--font-arabic);
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.25rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-body {
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #090d16;
            max-height: 65vh;
            overflow: auto;
        }

        .modal-body img {
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
            border-radius: var(--radius-sm);
        }

        .modal-footer {
            padding: 14px 20px;
            background: var(--bg-surface);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 16px; }
            .top-nav { padding: 12px 16px; flex-direction: column; gap: 12px; }
            .controls-panel { padding: 16px; }
            .vocab-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="brand-box">
            <div class="brand-icon">
                <i class="fa-solid fa-shapes"></i>
            </div>
            <div class="brand-text">
                <h1>منصة المفردات العربية المصورة</h1>
                <span>Revizy Seeder Arabic Vocabulary Studio</span>
            </div>
        </div>

        <div class="nav-actions">
            <a href="/admin/arabic-vocabularies" class="btn-nav">
                <i class="fa-solid fa-table-columns"></i>
                لوحة التحكم (Filament)
            </a>
            <a href="/seeder" class="btn-nav">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                الرجوع إلى Seeder
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">

        <!-- Top Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon emerald">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="stat-meta">
                    <h4>إجمالي المفردات المستخرجة</h4>
                    <div class="stat-val" id="stat-total">{{ $stats['total'] }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon sky">
                    <i class="fa-solid fa-image"></i>
                </div>
                <div class="stat-meta">
                    <h4>مفردات مصورة (بطاقات ورسوم)</h4>
                    <div class="stat-val" id="stat-images">{{ $stats['with_images'] }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div class="stat-meta">
                    <h4>المعجم المساعد (نصوص القراءة)</h4>
                    <div class="stat-val" id="stat-mojami">{{ $stats['strategies']['mojami'] }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon amber">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <div class="stat-meta">
                    <h4>شبكات المفردات وخرائط الكلمات</h4>
                    <div class="stat-val" id="stat-networks">{{ $stats['strategies']['network'] + $stats['strategies']['map'] }}</div>
                </div>
            </div>
        </div>

        <!-- Filter Controls Panel -->
        <div class="controls-panel">
            
            <!-- Grade Filter -->
            <div class="control-row">
                <span class="control-label">المستوى (N):</span>
                <div class="pills-group" id="grade-pills">
                    <button type="button" class="filter-pill {{ empty($grade) || $grade === 'all' ? 'active' : '' }}" data-filter="grade" data-value="all">الكل</button>
                    <button type="button" class="filter-pill {{ $grade === 'N1' ? 'active' : '' }}" data-filter="grade" data-value="N1">N1 (الأول)</button>
                    <button type="button" class="filter-pill {{ $grade === 'N2' ? 'active' : '' }}" data-filter="grade" data-value="N2">N2 (الثاني)</button>
                    <button type="button" class="filter-pill {{ $grade === 'N3' ? 'active' : '' }}" data-filter="grade" data-value="N3">N3 (الثالث)</button>
                    <button type="button" class="filter-pill {{ $grade === 'N4' ? 'active' : '' }}" data-filter="grade" data-value="N4">N4 (الرابع)</button>
                    <button type="button" class="filter-pill {{ $grade === 'N5' ? 'active' : '' }}" data-filter="grade" data-value="N5">N5 (الخامس)</button>
                    <button type="button" class="filter-pill {{ $grade === 'N6' ? 'active' : '' }}" data-filter="grade" data-value="N6">N6 (السادس)</button>
                </div>
            </div>

            <!-- Period Filter -->
            <div class="control-row">
                <span class="control-label">الفترة (P):</span>
                <div class="pills-group" id="period-pills">
                    <button type="button" class="filter-pill {{ empty($period) || $period === 'all' ? 'active' : '' }}" data-filter="period" data-value="all">الكل</button>
                    <button type="button" class="filter-pill {{ $period === 'P1' ? 'active' : '' }}" data-filter="period" data-value="P1">P1 (الأولى)</button>
                    <button type="button" class="filter-pill {{ $period === 'P2' ? 'active' : '' }}" data-filter="period" data-value="P2">P2 (الثانية)</button>
                    <button type="button" class="filter-pill {{ $period === 'P3' ? 'active' : '' }}" data-filter="period" data-value="P3">P3 (الثالثة)</button>
                    <button type="button" class="filter-pill {{ $period === 'P4' ? 'active' : '' }}" data-filter="period" data-value="P4">P4 (الرابعة)</button>
                    <button type="button" class="filter-pill {{ $period === 'P5' ? 'active' : '' }}" data-filter="period" data-value="P5">P5 (الخامسة)</button>
                </div>
            </div>

            <!-- Week Filter -->
            <div class="control-row">
                <span class="control-label">الأسبوع (SEM):</span>
                <div class="pills-group" id="week-pills">
                    <button type="button" class="filter-pill {{ empty($week) || $week === 'all' ? 'active' : '' }}" data-filter="week" data-value="all">الكل</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM1' ? 'active' : '' }}" data-filter="week" data-value="SEM1">SEM 1</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM2' ? 'active' : '' }}" data-filter="week" data-value="SEM2">SEM 2</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM3' ? 'active' : '' }}" data-filter="week" data-value="SEM3">SEM 3</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM4' ? 'active' : '' }}" data-filter="week" data-value="SEM4">SEM 4</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM5' ? 'active' : '' }}" data-filter="week" data-value="SEM5">SEM 5</button>
                    <button type="button" class="filter-pill {{ $week === 'SEM6' ? 'active' : '' }}" data-filter="week" data-value="SEM6">SEM 6</button>
                </div>
            </div>

            <!-- Strategy Filter -->
            <div class="control-row">
                <span class="control-label">الإستراتيجية:</span>
                <div class="pills-group" id="strategy-pills">
                    <button type="button" class="filter-pill {{ empty($strategy) || $strategy === 'all' ? 'active' : '' }}" data-filter="strategy" data-value="all">جميع الإستراتيجيات</button>
                    <button type="button" class="filter-pill {{ $strategy === 'معجم مصور' ? 'active' : '' }}" data-filter="strategy" data-value="معجم مصور">معجم مصور (بطاقات)</button>
                    <button type="button" class="filter-pill sky {{ $strategy === 'المعجم المساعد' ? 'active' : '' }}" data-filter="strategy" data-value="المعجم المساعد">المعجم المساعد (شرح نصوص)</button>
                    <button type="button" class="filter-pill purple {{ $strategy === 'شبكة المفردات' ? 'active' : '' }}" data-filter="strategy" data-value="شبكة المفردات">شبكة المفردات</button>
                    <button type="button" class="filter-pill amber {{ $strategy === 'خريطة الكلمة' ? 'active' : '' }}" data-filter="strategy" data-value="خريطة الكلمة">خريطة الكلمة</button>
                </div>
            </div>

            <!-- Search and Image Toggle -->
            <div class="search-toggle-bar">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="filter-search" class="search-input" placeholder="ابحث عن كلمة، شرح، جملة سياق أو معرف الدرس..." value="{{ $search }}">
                </div>

                <label class="toggle-switch-wrapper" id="toggle-has-image-btn">
                    <div class="toggle-switch {{ $hasImage === '1' ? 'checked' : '' }}" id="toggle-switch-ui"></div>
                    <span class="toggle-label">عرض المفردات ذات الصور فقط</span>
                </label>
            </div>
        </div>

        <!-- Results Header -->
        <div class="results-header">
            <div class="results-count">
                المعروض حالياً: <strong id="visible-count">{{ $items->count() }}</strong> مفردة تعليمية
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="vocab-grid" id="vocab-grid">
            @forelse ($items as $item)
                @php
                    $stratClass = 'gray';
                    if ($item->strategy === 'معجم مصور') $stratClass = 'emerald';
                    elseif ($item->strategy === 'المعجم المساعد') $stratClass = 'sky';
                    elseif ($item->strategy === 'شبكة المفردات') $stratClass = 'purple';
                    elseif ($item->strategy === 'خريطة الكلمة') $stratClass = 'amber';
                @endphp
                <div class="vocab-card" 
                     data-grade="{{ $item->grade }}"
                     data-period="{{ $item->period }}"
                     data-week="{{ $item->week }}"
                     data-strategy="{{ $item->strategy ?? 'أخرى' }}"
                     data-has-image="{{ $item->image_path ? '1' : '0' }}"
                     data-search="{{ mb_strtolower($item->word . ' ' . $item->raw_word . ' ' . $item->example_sentence . ' ' . $item->lesson_id) }}">
                    
                    <!-- Image Box -->
                    <div class="card-img-box" onclick="openImageModal('{{ $item->image_path ? asset($item->image_path) : '' }}', '{{ addslashes($item->word) }}', '{{ addslashes($item->example_sentence ?? '') }}')">
                        @if ($item->image_path)
                            <img src="{{ asset($item->image_path) }}" alt="{{ $item->word }}" loading="lazy">
                            <div class="zoom-overlay">
                                <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                            </div>
                        @else
                            <div class="no-img-placeholder">
                                <i class="fa-solid fa-book-bookmark"></i>
                                <span>بدون بطاقة مصورة</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="card-content">
                        <div class="card-header-line">
                            <div class="word-vocalized">{{ $item->word }}</div>
                            @if ($item->strategy)
                                <span class="strategy-badge {{ $stratClass }}">
                                    @if ($item->strategy === 'معجم مصور') <i class="fa-solid fa-image"></i>
                                    @elseif ($item->strategy === 'المعجم المساعد') <i class="fa-solid fa-spell-check"></i>
                                    @elseif ($item->strategy === 'شبكة المفردات') <i class="fa-solid fa-network-wired"></i>
                                    @elseif ($item->strategy === 'خريطة الكلمة') <i class="fa-solid fa-diagram-project"></i>
                                    @endif
                                    {{ $item->strategy }}
                                </span>
                            @endif
                        </div>

                        <div class="raw-word-box">
                            <small>بدون تشكيل:</small>
                            <span>{{ $item->raw_word }}</span>
                        </div>

                        @if ($item->example_sentence)
                            <div class="context-box {{ $stratClass }}-border">
                                <small>
                                    @if ($item->strategy === 'المعجم المساعد')
                                        الشرح / المرادف:
                                    @elseif ($item->strategy === 'شبكة المفردات')
                                        شبكة الكلمات:
                                    @else
                                        جملة السياق / الشرح:
                                    @endif
                                </small>
                                {{ $item->example_sentence }}
                            </div>
                        @endif

                        <div class="card-footer-meta">
                            <div class="scope-pills">
                                <span class="scope-pill">{{ $item->grade }}</span>
                                <span class="scope-pill">{{ $item->period }}</span>
                                <span class="scope-pill">{{ $item->week }}</span>
                                @if ($item->slide_index)
                                    <span class="scope-pill">شريحة {{ $item->slide_index }}</span>
                                @endif
                            </div>
                            <div class="lesson-code" title="{{ $item->lesson_id }}">{{ $item->lesson_id }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-folder-open"></i>
                    <h3>لا توجد مفردات تطابق هذه التصفية</h3>
                    <p>جرب اختيار مستوى أو فترة أخرى أو إعادة ضبط عوامل التصفية.</p>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Image Zoom Modal -->
    <div class="modal-backdrop" id="imageModal" onclick="closeImageModal(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">المفردة</div>
                <button type="button" class="modal-close" onclick="closeImageModal()">&times;</button>
            </div>
            <div class="modal-body">
                <img id="modalImg" src="" alt="">
            </div>
            <div class="modal-footer">
                <span id="modalDescription"></span>
                <button type="button" class="btn-nav" onclick="closeImageModal()">إغلاق</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Filter & Modal Logic -->
    <script>
        const state = {
            grade: '{{ $grade ?? "all" }}',
            period: '{{ $period ?? "all" }}',
            week: '{{ $week ?? "all" }}',
            strategy: '{{ $strategy ?? "all" }}',
            hasImage: {{ $hasImage === '1' ? 'true' : 'false' }},
            search: '{{ $search ?? "" }}'
        };

        // Attach listeners to pill buttons
        document.querySelectorAll('.filter-pill').forEach(btn => {
            btn.addEventListener('click', function () {
                const filter = this.getAttribute('data-filter');
                const val = this.getAttribute('data-value');
                
                // Active toggle in button group
                const group = this.closest('.pills-group');
                group.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                state[filter] = val;
                applyClientFilter();
            });
        });

        // Search Input
        const searchInput = document.getElementById('filter-search');
        let searchTimeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.search = this.value.trim().toLowerCase();
                applyClientFilter();
            }, 120);
        });

        // Image Toggle
        const toggleBtn = document.getElementById('toggle-has-image-btn');
        const toggleSwitchUI = document.getElementById('toggle-switch-ui');
        toggleBtn.addEventListener('click', function () {
            state.hasImage = !state.hasImage;
            toggleSwitchUI.classList.toggle('checked', state.hasImage);
            applyClientFilter();
        });

        // Client-side instant filtering
        function applyClientFilter() {
            const cards = document.querySelectorAll('.vocab-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cGrade = card.getAttribute('data-grade');
                const cPeriod = card.getAttribute('data-period');
                const cWeek = card.getAttribute('data-week');
                const cStrat = card.getAttribute('data-strategy');
                const cHasImg = card.getAttribute('data-has-image') === '1';
                const cSearch = card.getAttribute('data-search') || '';

                let match = true;

                if (state.grade !== 'all' && cGrade !== state.grade) match = false;
                if (state.period !== 'all' && cPeriod !== state.period) match = false;
                if (state.week !== 'all' && cWeek !== state.week) match = false;
                if (state.strategy !== 'all' && cStrat !== state.strategy) match = false;
                if (state.hasImage && !cHasImg) match = false;
                if (state.search && !cSearch.includes(state.search)) match = false;

                if (match) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('visible-count').textContent = visibleCount;
        }

        // Image Lightbox Modal
        function openImageModal(imgSrc, word, description) {
            if (!imgSrc) return;
            document.getElementById('modalImg').src = imgSrc;
            document.getElementById('modalTitle').textContent = word;
            document.getElementById('modalDescription').textContent = description || '';
            const modal = document.getElementById('imageModal');
            modal.classList.add('show');
        }

        function closeImageModal(e) {
            if (e && e.target !== document.getElementById('imageModal') && !e.target.classList.contains('modal-close')) {
                return;
            }
            const modal = document.getElementById('imageModal');
            modal.classList.remove('show');
            document.getElementById('modalImg').src = '';
        }

        // Close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>
