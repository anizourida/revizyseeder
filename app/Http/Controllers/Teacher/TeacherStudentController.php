<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();
        $students = $teacher->students()->latest()->get();

        return view('teacher.students', compact('teacher', 'students'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $request->validate([
            'student_code' => ['required', 'string', 'max:100'],
            'student_name' => ['nullable', 'string', 'max:255'],
        ], [
            'student_code.required' => 'Le code de l\'élève est obligatoire.',
        ]);

        // Check if student is already linked to this teacher
        $exists = $teacher->students()
            ->where('student_code', $request->student_code)
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_code' => 'Cet élève est déjà ajouté à votre liste.']);
        }

        $teacher->students()->create([
            'student_code' => $request->student_code,
            'student_name' => $request->student_name,
        ]);

        return back()->with('success', 'L\'élève a été ajouté avec succès.');
    }

    public function destroy(int $id)
    {
        $teacher = Auth::guard('teacher')->user();

        $student = $teacher->students()->findOrFail($id);
        $student->delete();

        return back()->with('success', 'L\'élève a été retiré de votre liste.');
    }
}
