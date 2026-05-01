<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RaiidaUiController extends Controller
{
    /** @var array<string, string> */
    private const INDEX_MODULE_MAP = [
        'dashboard' => 'dashboard',
        'files' => 'files',
        'browser' => 'browser',
        'vocabulary' => 'vocab',
        'roadmap' => 'roadmap',
        'grammaire' => 'grammaire',
        'audios' => 'audios',
        'assets' => 'assets',
        'flashcards-uploader' => 'flashcards-uploader',
        'concept-creator' => 'concept-creator',
        'questions-studio' => 'questions-studio',
        'conjugaison' => 'conjugaison',
    ];

    public function index(): RedirectResponse
    {
        return redirect()->route('raiida.module', ['module' => 'dashboard']);
    }

    public function module(string $module): View
    {
        if ($module === 'question-studio') {
            $module = 'questions-studio';
        }

        if (array_key_exists($module, self::INDEX_MODULE_MAP)) {
            return view('raiida.app', [
                'activeModule' => $module,
                'initialView' => self::INDEX_MODULE_MAP[$module],
                'apiBase' => '/api',
            ]);
        }

        abort(404);
    }
}
