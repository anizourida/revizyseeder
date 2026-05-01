<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raiida Content Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('raiida-ui/css/style.css') }}">
</head>

<body data-initial-view="{{ $initialView ?? 'dashboard' }}" data-api-base="{{ $apiBase ?? '/api' }}">
    <div class="app-container">
        @include('raiida.partials.sidebar', ['activeModule' => $activeModule ?? 'dashboard'])
        @include('raiida.partials.main-content')
    </div>

    @include('raiida.partials.preview-modal')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>window.RAIIDA_API_BASE = @json($apiBase ?? '/api');</script>
    <script src="{{ asset('raiida-ui/js/common.js') }}"></script>
    <script src="{{ asset('raiida-ui/js/app.js') }}"></script>
</body>

</html>
