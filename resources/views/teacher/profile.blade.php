@extends('teacher.layout')
@section('title', 'Mon profil')

@section('content')
<div class="main">
    <div class="page-header">
        <h1>Mon profil</h1>
    </div>

    <!-- Profile info card -->
    <div class="card">
        <div class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Informations personnelles
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.profile.update') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name', $teacher->name) }}"
                       required>
                @error('name')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email_display">Adresse e-mail</label>
                <input type="email"
                       id="email_display"
                       class="form-input"
                       value="{{ $teacher->email }}"
                       disabled
                       style="background: #f7f8fa; color: var(--text-muted);">
                <div class="form-hint">L'adresse e-mail ne peut pas être modifiée.</div>
            </div>

            <hr class="divider">

            <div class="card-title" style="margin-top: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Changer le mot de passe
            </div>
            <p class="text-muted text-sm mb-4">Laissez ces champs vides si vous ne souhaitez pas changer de mot de passe.</p>

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       class="form-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                       placeholder="••••••••">
                @error('current_password')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password"
                           id="new_password"
                           name="new_password"
                           class="form-input {{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                           placeholder="Minimum 8 caractères">
                    @error('new_password')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirmer</label>
                    <input type="password"
                           id="new_password_confirmation"
                           name="new_password_confirmation"
                           class="form-input"
                           placeholder="Retapez le mot de passe">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>

    <!-- Account info -->
    <div class="card">
        <div class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            Informations du compte
        </div>
        <div style="display: grid; gap: 12px; font-size: 0.9375rem;">
            <div>
                <span class="text-muted">Compte créé le :</span>
                <strong>{{ $teacher->created_at->format('d/m/Y à H:i') }}</strong>
            </div>
            <div>
                <span class="text-muted">E-mail vérifié :</span>
                @if($teacher->hasVerifiedEmail())
                    <strong style="color: var(--accent);">✓ Oui ({{ $teacher->email_verified_at->format('d/m/Y') }})</strong>
                @else
                    <strong style="color: var(--red);">✗ Non</strong>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
