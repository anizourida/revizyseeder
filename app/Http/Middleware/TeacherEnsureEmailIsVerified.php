<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherEnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $teacher = auth('teacher')->user();

        if (!$teacher || !$teacher->hasVerifiedEmail()) {
            return redirect()->route('teacher.verification.notice')
                ->with('warning', 'Veuillez vérifier votre adresse e-mail avant de continuer.');
        }

        return $next($request);
    }
}
