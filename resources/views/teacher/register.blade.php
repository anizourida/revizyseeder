@extends('teacher.layout')
@section('title', 'Inscription')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-logo">Revizy</div>
            <h1>Créer un compte</h1>
            <p>Espace réservé aux enseignants @taalim.ma</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.register') }}" id="registerForm">
            @csrf
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}"
                       placeholder="Prénom Nom"
                       required
                       autofocus>
                @error('name')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="prenom.nom@taalim.ma"
                       required>
                <div class="form-hint">L'adresse doit se terminer par @taalim.ma</div>
                @error('email')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Minimum 8 caractères"
                       required>
                @error('password')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input"
                       placeholder="Retapez votre mot de passe"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
        </form>

        <div class="auth-links">
            Vous avez déjà un compte ?
            <a href="{{ route('teacher.login') }}">Se connecter</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Client-side email domain check
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        if (!email.endsWith('@taalim.ma')) {
            e.preventDefault();
            const emailInput = document.getElementById('email');
            emailInput.classList.add('is-invalid');
            let hint = emailInput.parentElement.querySelector('.validation-error');
            if (!hint) {
                hint = document.createElement('div');
                hint.className = 'validation-error';
                emailInput.parentElement.appendChild(hint);
            }
            hint.textContent = "L'adresse e-mail doit se terminer par @taalim.ma.";
        }
    });
</script>
@endsection
