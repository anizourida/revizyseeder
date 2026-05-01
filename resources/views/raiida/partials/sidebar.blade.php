<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon"><i class="fa-solid fa-shapes"></i></div>
        <h1>Raiida</h1>
    </div>

    <nav class="nav-menu">
        <a class="nav-item {{ ($activeModule ?? '') === 'dashboard' ? 'active' : '' }}" data-view="dashboard" href="{{ route('raiida.module', ['module' => 'dashboard']) }}">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'roadmap' ? 'active' : '' }}" data-view="roadmap" href="{{ route('raiida.module', ['module' => 'roadmap']) }}">
            <i class="fa-solid fa-calendar-days"></i> Roadmap
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'files' ? 'active' : '' }}" data-view="files" href="{{ route('raiida.module', ['module' => 'files']) }}">
            <i class="fa-solid fa-table-list"></i> Fichiers
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'browser' ? 'active' : '' }}" data-view="browser" href="{{ route('raiida.module', ['module' => 'browser']) }}">
            <i class="fa-solid fa-folder-tree"></i> Navigateur
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'grammaire' ? 'active' : '' }}" data-view="grammaire" href="{{ route('raiida.module', ['module' => 'grammaire']) }}">
            <i class="fa-solid fa-pen-nib"></i> Grammaire
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'conjugaison' ? 'active' : '' }}" data-view="conjugaison" href="{{ route('raiida.module', ['module' => 'conjugaison']) }}">
            <i class="fa-solid fa-book"></i> Conjugaison
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'vocabulary' ? 'active' : '' }}" data-view="vocab" href="{{ route('raiida.module', ['module' => 'vocabulary']) }}">
            <i class="fa-solid fa-book-open"></i> Vocabulaire
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'audios' ? 'active' : '' }}" data-view="audios" href="{{ route('raiida.module', ['module' => 'audios']) }}">
            <i class="fa-solid fa-music"></i> Audios
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'assets' ? 'active' : '' }}" data-view="assets" href="{{ route('raiida.module', ['module' => 'assets']) }}">
            <i class="fa-solid fa-images"></i> Assets
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'flashcards-uploader' ? 'active' : '' }}" data-view="flashcards-uploader" href="{{ route('raiida.module', ['module' => 'flashcards-uploader']) }}">
            <i class="fa-solid fa-cloud-arrow-up"></i> Flashcards Uploader
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'concept-creator' ? 'active' : '' }}" data-view="concept-creator" href="{{ route('raiida.module', ['module' => 'concept-creator']) }}">
            <i class="fa-solid fa-lightbulb"></i> Concept creator
        </a>
        <a class="nav-item {{ ($activeModule ?? '') === 'questions-studio' ? 'active' : '' }}" data-view="questions-studio" href="{{ route('raiida.module', ['module' => 'questions-studio']) }}">
            <i class="fa-solid fa-circle-question"></i> Questions Studio
        </a>
    </nav>

    <div class="sidebar-footer">
        <p>Status: <span id="connection-status" class="status-online">En ligne</span></p>
        <button id="btn-auth-session" type="button" class="btn btn-secondary" style="margin-top: 8px; width: 100%;">
            <i class="fa-solid fa-user-shield"></i> Session API
        </button>
    </div>
</aside>
