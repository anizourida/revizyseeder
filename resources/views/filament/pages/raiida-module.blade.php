<x-filament-panels::page>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('raiida-ui/css/style.filament.css') }}">

    <style>
        #raiida-root.raiida-legacy {
            height: auto;
            min-height: calc(100vh - 12rem);
            overflow: visible;
            border: 1px solid rgb(226 232 240 / 1);
            border-radius: 0.75rem;
        }

        #raiida-root .main-content {
            max-height: none;
        }

        #raiida-root .raiida-session-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-white);
        }
    </style>

    <script>
        window.RAIIDA_INITIAL_VIEW = @json($this->getInitialView());
        window.RAIIDA_API_BASE = @json($this->getApiBase());
    </script>

    <div
        id="raiida-root"
        class="raiida-legacy"
        data-initial-view="{{ $this->getInitialView() }}"
        data-api-base="{{ $this->getApiBase() }}"
    >
        <div class="raiida-session-toolbar">
            <div style="font-size: 0.9rem; color: var(--text-secondary);">
                Status: <span id="connection-status" class="status-online">En ligne</span>
            </div>
            <button id="btn-auth-session" type="button" class="btn btn-secondary">
                <i class="fa-solid fa-user-shield"></i> Session API
            </button>
        </div>

        @include('raiida.partials.main-content')
        @include('raiida.partials.preview-modal')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('raiida-ui/js/common.js') }}"></script>
    <script src="{{ asset('raiida-ui/js/app.js') }}"></script>
</x-filament-panels::page>
