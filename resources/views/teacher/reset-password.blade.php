@extends('teacher.layout')
@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-logo">Revizy</div>
            <h1>Réinitialiser le mot de passe</h1>
            <p>Choisissez un nouveau mot de passe</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email', $email) }}"
                       placeholder="prenom.nom@taalim.ma"
                       required>
                @error('email')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
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

            <button type="submit" class="btn btn-primary btn-block">Réinitialiser le mot de passe</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('teacher.login') }}">← Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection
