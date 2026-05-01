<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\TeacherVerificationMail;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class TeacherAuthController extends Controller
{
    // ─── Login ──────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.students.index');
        }

        return view('teacher.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@taalim.ma'],
            'password' => ['required'],
        ], [
            'email.ends_with' => 'L\'adresse e-mail doit se terminer par @taalim.ma.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('teacher')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('teacher.students.index'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Les identifiants fournis sont incorrects.']);
    }

    // ─── Register ───────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.students.index');
        }

        return view('teacher.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'ends_with:@taalim.ma', 'unique:teachers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.ends_with' => 'L\'adresse e-mail doit se terminer par @taalim.ma.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $token = Str::random(64);

        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'verification_token' => $token,
        ]);

        $verificationUrl = route('teacher.verification.verify', ['token' => $token]);

        Mail::to($teacher->email)->send(new TeacherVerificationMail($teacher, $verificationUrl));

        Auth::guard('teacher')->login($teacher);

        return redirect()->route('teacher.verification.notice')
            ->with('success', 'Inscription réussie ! Veuillez vérifier votre adresse e-mail.');
    }

    // ─── Logout ─────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::guard('teacher')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('teacher.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    // ─── Forgot Password ────────────────────────────────────

    public function showForgotPassword()
    {
        return view('teacher.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@taalim.ma'],
        ], [
            'email.ends_with' => 'L\'adresse e-mail doit se terminer par @taalim.ma.',
        ]);

        $status = Password::broker('teachers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Impossible d\'envoyer le lien de réinitialisation. Vérifiez votre adresse e-mail.']);
    }

    // ─── Reset Password ─────────────────────────────────────

    public function showResetPassword(Request $request, string $token)
    {
        return view('teacher.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'ends_with:@taalim.ma'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.ends_with' => 'L\'adresse e-mail doit se terminer par @taalim.ma.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $status = Password::broker('teachers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Teacher $teacher, string $password) {
                $teacher->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $teacher->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('teacher.login')
                ->with('success', 'Votre mot de passe a été réinitialisé avec succès.');
        }

        return back()->withErrors(['email' => 'Le lien de réinitialisation est invalide ou expiré.']);
    }
}
