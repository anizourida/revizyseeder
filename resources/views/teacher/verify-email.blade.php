@extends('teacher.layout')
@section('title', 'Vérification e-mail')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card" style="text-align: center;">
        <div style="margin-bottom: 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
        </div>
        <h1 style="font-size: 1.4rem; font-weight: 700; color: var(--text); margin-bottom: 12px;">
            Vérifiez votre adresse e-mail
        </h1>
        <p style="color: var(--text-muted); margin-bottom: 8px; font-size: 0.9375rem; line-height: 1.6;">
            Un lien de vérification a été envoyé à votre adresse e-mail
        </p>
        @auth('teacher')
        <p style="font-weight: 600; color: var(--primary); margin-bottom: 24px; font-size: 0.9375rem;">
            {{ auth('teacher')->user()->email }}
        </p>
        @endauth
        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 28px; line-height: 1.6;">
            Cliquez sur le lien dans l'e-mail pour activer votre compte. Si vous ne trouvez pas l'e-mail, vérifiez votre dossier de spam.
        </p>

        <form method="POST" action="{{ route('teacher.verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-accent btn-block">
                Renvoyer l'e-mail de vérification
            </button>
        </form>

        <div class="auth-links" style="margin-top: 20px;">
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: var(--primary); cursor: pointer; font-weight: 500; font-size: 0.875rem; font-family: inherit;">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
