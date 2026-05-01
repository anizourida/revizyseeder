@extends('teacher.layout')
@section('title', 'Connexion')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-logo">Revizy</div>
            <h1>Connexion Enseignant</h1>
            <p>Accédez à votre espace personnel</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.login') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="prenom.nom@taalim.ma"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input"
                       placeholder="••••••••"
                       required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
                <label class="form-checkbox" style="margin-bottom: 0;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Se souvenir de moi</span>
                </label>
                <a href="{{ route('teacher.password.request') }}" style="font-size: 0.8125rem; color: var(--primary); text-decoration: none; font-weight: 500;">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <div class="auth-links">
            Vous n'avez pas de compte ?
            <a href="{{ route('teacher.register') }}">Créer un compte</a>
        </div>
    </div>
</div>
@endsection
