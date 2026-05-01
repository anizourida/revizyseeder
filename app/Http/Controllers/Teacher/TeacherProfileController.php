<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherProfileController extends Controller
{
    public function show()
    {
        $teacher = Auth::guard('teacher')->user();

        return view('teacher.profile', compact('teacher'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        $messages = [];

        // Only validate password fields if trying to change password
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = ['required'];
            $rules['new_password'] = ['required', 'string', 'min:8', 'confirmed'];

            $messages['new_password.confirmed'] = 'Les mots de passe ne correspondent pas.';
            $messages['new_password.min'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        $request->validate($rules, $messages);

        // Verify current password if changing password
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $teacher->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
        }

        $teacher->name = $request->name;

        if ($request->filled('new_password')) {
            $teacher->password = $request->new_password;
        }

        $teacher->save();

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
