@extends('teacher.layout')
@section('title', 'Mot de passe oublié')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-card-logo">Revizy</div>
            <h1>Mot de passe oublié</h1>
            <p>Recevez un lien de réinitialisation par e-mail</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.password.email') }}">
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
                @error('email')
                    <div class="validation-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block">Envoyer le lien de réinitialisation</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('teacher.login') }}">← Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection
