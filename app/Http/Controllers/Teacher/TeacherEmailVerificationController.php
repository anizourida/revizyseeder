<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Mail\TeacherVerificationMail;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeacherEmailVerificationController extends Controller
{
    public function notice()
    {
        $teacher = Auth::guard('teacher')->user();

        if ($teacher && $teacher->hasVerifiedEmail()) {
            return redirect()->route('teacher.students.index');
        }

        return view('teacher.verify-email');
    }

    public function verify(string $token)
    {
        $teacher = Teacher::where('verification_token', $token)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['token' => 'Le lien de vérification est invalide ou a déjà été utilisé.']);
        }

        $teacher->markEmailAsVerified();

        // Log the teacher in if not already
        if (!Auth::guard('teacher')->check()) {
            Auth::guard('teacher')->login($teacher);
        }

        return redirect()->route('teacher.students.index')
            ->with('success', 'Votre adresse e-mail a été vérifiée avec succès !');
    }

    public function resend(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        if (!$teacher) {
            return redirect()->route('teacher.login');
        }

        if ($teacher->hasVerifiedEmail()) {
            return redirect()->route('teacher.students.index');
        }

        // Generate a new token
        $token = Str::random(64);
        $teacher->forceFill(['verification_token' => $token])->save();

        $verificationUrl = route('teacher.verification.verify', ['token' => $token]);

        Mail::to($teacher->email)->send(new TeacherVerificationMail($teacher, $verificationUrl));

        return back()->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.');
    }
}
