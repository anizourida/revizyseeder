<?php

use App\Http\Controllers\Web\FileAssetOpenController;
use App\Http\Controllers\Web\FileAssetPresentationPreviewController;
use App\Http\Controllers\Web\RaiidaUiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'status' => 'ok',
    ]);
});

Route::get('/raiida', [RaiidaUiController::class, 'index'])->name('raiida.index');
Route::get('/raiida/question-studio', fn () => redirect()->route('raiida.module', ['module' => 'questions-studio']));
Route::get('/roadmap.html', fn () => redirect()->route('raiida.module', ['module' => 'roadmap']));
Route::get('/grammaire.html', fn () => redirect()->route('raiida.module', ['module' => 'grammaire']));
Route::get('/conjugaison.html', fn () => redirect()->route('raiida.module', ['module' => 'conjugaison']));

Route::get('/raiida/{module}', [RaiidaUiController::class, 'module'])
    ->where('module', 'dashboard|files|browser|vocabulary|audios|assets|flashcards-uploader|concept-creator|questions-studio|conjugaison|grammaire|roadmap')
    ->name('raiida.module');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin/files/open/{fileAsset}', FileAssetOpenController::class)->name('admin.files.open');
    Route::get('/admin/files/preview/{fileAsset}', [FileAssetPresentationPreviewController::class, 'show'])
        ->name('admin.files.preview');
    Route::get('/admin/files/preview/{fileAsset}/asset/{assetPath}', [FileAssetPresentationPreviewController::class, 'asset'])
        ->where('assetPath', '.*')
        ->name('admin.files.preview.asset');

    Route::get('/ocr/view/{page}/{model?}', function (\App\Models\Raiida\Page $page, ?string $model = 'olmocr') {
        $pathCol = ($model === 'chandra') ? 'ocr_chandra_path' : 'ocr_olmocr_path';
        $path = $page->{$pathCol};

        if (!$path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            abort(404, 'OCR content for ' . $model . ' not found.');
        }
        return response(\Illuminate\Support\Facades\Storage::disk('local')->get($path))
            ->header('Content-Type', 'text/html');
    })->name('ocr.view');
});

// ═══════════════════════════════════════════════════════════
// Teacher Panel Routes
// ═══════════════════════════════════════════════════════════

use App\Http\Controllers\Teacher\TeacherAuthController;
use App\Http\Controllers\Teacher\TeacherEmailVerificationController;
use App\Http\Controllers\Teacher\TeacherProfileController;
use App\Http\Controllers\Teacher\TeacherStudentController;

// When TEACHER_DOMAIN is set (e.g. teachers.revizyapp.com), routes are served
// at the root of that domain. Otherwise, they fall under the /teacher prefix.
$teacherDomain = env('TEACHER_DOMAIN');
$teacherRoutes = function () {

    // Root redirect → login
    Route::get('/', fn () => redirect()->route('teacher.login'));

    // ─── Guest routes (login, register, forgot/reset password) ────
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('/login', [TeacherAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [TeacherAuthController::class, 'login']);

        Route::get('/register', [TeacherAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [TeacherAuthController::class, 'register']);

        Route::get('/forgot-password', [TeacherAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [TeacherAuthController::class, 'sendResetLink'])->name('password.email');

        Route::get('/reset-password/{token}', [TeacherAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [TeacherAuthController::class, 'resetPassword'])->name('password.update');
    });

    // ─── Email verification (public token link) ──────────────────
    Route::get('/verify-email/{token}', [TeacherEmailVerificationController::class, 'verify'])
        ->name('verification.verify');

    // ─── Authenticated (but possibly unverified) ─────────────────
    Route::middleware('teacher.auth')->group(function () {
        Route::post('/logout', [TeacherAuthController::class, 'logout'])->name('logout');

        Route::get('/verify-email', [TeacherEmailVerificationController::class, 'notice'])
            ->name('verification.notice');
        Route::post('/verify-email/resend', [TeacherEmailVerificationController::class, 'resend'])
            ->middleware('throttle:3,1')
            ->name('verification.resend');
    });

    // ─── Authenticated + verified ────────────────────────────────
    Route::middleware(['teacher.auth', 'teacher.verified'])->group(function () {
        Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');
        Route::post('/students', [TeacherStudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{id}', [TeacherStudentController::class, 'destroy'])->name('students.destroy');

        Route::get('/profile', [TeacherProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');
    });
};

if ($teacherDomain) {
    // Production: teachers.revizyapp.com → routes at root
    Route::domain($teacherDomain)->name('teacher.')->group($teacherRoutes);
} else {
    // Local dev: /teacher prefix
    Route::prefix('teacher')->name('teacher.')->group($teacherRoutes);
}
