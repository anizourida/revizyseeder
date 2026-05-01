<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('teacher')->check()) {
            return redirect()->route('teacher.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        return $next($request);
    }
}
