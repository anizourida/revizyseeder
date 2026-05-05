<?php

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Raiida\Page;

$mode = $_GET['mode'] ?? 'manual'; // 'manual' or 'review'

// Handle Save, Skip or Confirm Request
if (isset($_POST['action'])) {
    $pageId = $_POST['page_id'];
    $currentMode = $_POST['mode'] ?? 'manual';
    
    if ($_POST['action'] === 'save') {
        $pageNum = $_POST['page_number'];
        $page = Page::find($pageId);
        if ($page) {
            $page->update([
                'page_number' => $pageNum,
                'page_number_extraction_method' => 'admin_manually'
            ]);
        }
    } elseif ($_POST['action'] === 'skip') {
        $page = Page::find($pageId);
        if ($page) {
            $page->update([
                'page_number' => null,
                'page_number_extraction_method' => 'skipped'
            ]);
        }
    } elseif ($_POST['action'] === 'confirm') {
        // Confirm python_ocr result as correct
        $page = Page::find($pageId);
        if ($page) {
            $page->update([
                'page_number_extraction_method' => 'python_ocr_confirmed'
            ]);
        }
    }
    header("Location: rapid-labeling.php?mode={$currentMode}&success=1");
    exit;
}

if ($mode === 'review') {
    // Review mode: show python_ocr pages for double-checking
    $record = Page::where('page_number_extraction_method', 'python_ocr')
        ->orderBy('n_p_sem')
        ->orderBy('image_path')
        ->first();
    
    $remaining = Page::where('page_number_extraction_method', 'python_ocr')->count();
} else {
    // Manual mode: show pages needing manual labeling
    $record = Page::where(function($q) {
            $q->whereNull('page_number')->orWhere('page_number', '');
        })
        ->where(function($q) {
            $q->whereNull('page_number_extraction_method')
              ->orWhereNotIn('page_number_extraction_method', ['skipped', 'python_ocr']);
        })
        ->orderBy('n_p_sem')
        ->orderBy('image_path')
        ->first();

    $remaining = Page::where(function($q) {
        $q->whereNull('page_number')->orWhere('page_number', '');
    })
    ->where(function($q) {
        $q->whereNull('page_number_extraction_method')
          ->orWhereNotIn('page_number_extraction_method', ['skipped', 'python_ocr']);
    })
    ->count();
}

$pythonOcrCount = Page::where('page_number_extraction_method', 'python_ocr')->count();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapid Page Labeling</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        .image-container { height: 100vh; background: #0a0a0a; }
        .control-panel { height: 100vh; background: #ffffff; border-left: 1px solid #e5e5e5; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex">
        <!-- LEFT: Image -->
        <div class="flex-1 image-container flex items-center justify-center p-4 relative">
            <?php if ($record): ?>
                <img src="/presentation_data/<?= ltrim($record->image_path, 'presentation_data/') ?>" 
                     class="max-h-full max-w-full object-contain shadow-[0_0_50px_rgba(0,0,0,0.5)] rounded-sm"
                     id="main-image">
                
                <div class="absolute top-6 left-6 flex gap-3">
                    <div class="bg-black/60 backdrop-blur-lg px-4 py-2 rounded-xl text-white text-sm border border-white/10 font-medium">
                        <span class="opacity-60">Remaining:</span> <?= $remaining ?>
                    </div>
                    <?php if ($mode === 'review'): ?>
                        <div class="bg-amber-500/80 backdrop-blur-lg px-4 py-2 rounded-xl text-white text-sm font-bold">
                            🐍 REVIEW MODE
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-white text-center">
                    <h2 class="text-3xl font-bold mb-2">🎉 All Done!</h2>
                    <p class="opacity-60"><?= $mode === 'review' ? 'All Python OCR results have been reviewed.' : 'Every page has been labeled.' ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Control Panel -->
        <div class="w-[400px] control-panel flex flex-col p-8 shadow-2xl">
            <?php if ($record): ?>
                <div class="mb-6">
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                        <?= $mode === 'review' ? '🐍 Double Check' : 'Rapid Labeling' ?>
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        <?= $mode === 'review' ? 'Verify Python OCR results' : 'Manual Textbook Indexing' ?>
                    </p>
                </div>

                <!-- Mode Switcher -->
                <div class="flex gap-2 mb-6">
                    <a href="?mode=manual" 
                       class="flex-1 text-center py-2 rounded-xl text-sm font-bold transition-all
                              <?= $mode === 'manual' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>">
                        ✏️ Manual
                    </a>
                    <a href="?mode=review" 
                       class="flex-1 text-center py-2 rounded-xl text-sm font-bold transition-all relative
                              <?= $mode === 'review' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>">
                        🐍 Review OCR
                        <?php if ($pythonOcrCount > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center"><?= $pythonOcrCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="space-y-4 mb-auto">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Current Lesson</label>
                        <p class="text-sm font-bold text-gray-800 break-all"><?= $record->n_p_sem ?></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Grade</label>
                        <p class="text-sm font-bold text-gray-800"><?= $record->grade?->name ?? 'N/A' ?></p>
                    </div>

                    <form action="rapid-labeling.php" method="POST" id="label-form" class="mt-6">
                        <input type="hidden" name="page_id" value="<?= $record->id ?>">
                        <input type="hidden" name="mode" value="<?= $mode ?>">
                        
                        <?php if ($mode === 'review'): ?>
                            <!-- Review Mode: Show the Python OCR result -->
                            <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-4 mb-4">
                                <label class="block text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">🐍 Python OCR detected</label>
                                <p class="text-5xl font-black text-center text-amber-700"><?= $record->page_number ?></p>
                            </div>
                            
                            <label class="block text-sm font-bold text-gray-900 mb-3 text-center">Correct it or confirm</label>
                            <input type="text" 
                                   name="page_number" 
                                   id="page_input"
                                   value="<?= $record->page_number ?>"
                                   autocomplete="off"
                                   autofocus
                                   class="w-full text-5xl text-center font-black py-6 bg-white border-4 border-gray-100 rounded-3xl transition-all duration-200">
                            
                            <div class="mt-6 flex flex-col gap-3">
                                <div class="flex gap-3">
                                    <button type="submit" name="action" value="confirm"
                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-all active:scale-95">
                                        ✓ Confirm
                                    </button>
                                    <button type="submit" name="action" value="save"
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-all active:scale-95">
                                        Fix & Save
                                    </button>
                                </div>
                                <button type="submit" name="action" value="skip"
                                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-500 font-bold py-4 rounded-2xl transition-all"
                                        id="skip_button">
                                    SKIP (Mark as Unlabeled)
                                </button>
                            </div>
                        <?php else: ?>
                            <!-- Manual Mode -->
                            <label class="block text-sm font-bold text-gray-900 mb-3 text-center">PAGE NUMBER</label>
                            <input type="text" 
                                   name="page_number" 
                                   id="page_input"
                                   autocomplete="off"
                                   autofocus
                                   class="w-full text-7xl text-center font-black py-8 bg-white border-4 border-gray-100 rounded-3xl transition-all duration-200">
                            
                            <div class="mt-8 flex gap-3">
                                <button type="submit" name="action" value="save"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-all active:scale-95">
                                    SAVE (Enter)
                                </button>
                                <button type="submit" name="action" value="skip"
                                        class="w-24 bg-gray-200 hover:bg-gray-300 text-gray-500 font-bold py-4 rounded-2xl transition-all"
                                        id="skip_button">
                                    SKIP
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-6 text-center text-xs text-gray-400 space-y-2">
                            <p>Press <span class="bg-gray-200 px-2 py-1 rounded font-bold text-gray-600">ENTER</span> to <?= $mode === 'review' ? 'Confirm' : 'Save' ?> & Next</p>
                            <p>Press <span class="bg-gray-200 px-2 py-1 rounded font-bold text-gray-600">ESC</span> to Skip</p>
                        </div>
                    </form>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[10px] font-bold text-gray-300 tracking-widest">
                        <span>REVIZY ENGINE</span>
                        <span>v3.0</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center gap-4">
                    <?php if ($mode === 'review' && $remaining === 0): ?>
                        <p class="text-gray-500 text-center">All Python OCR results reviewed!</p>
                        <a href="?mode=manual" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold">Switch to Manual Mode</a>
                    <?php else: ?>
                        <a href="/admin" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold">Back to Dashboard</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.onload = () => {
            const input = document.getElementById('page_input');
            if (input) { input.focus(); input.select(); }
        };
        document.addEventListener('click', (e) => {
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                const input = document.getElementById('page_input');
                if (input) input.focus();
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const skip = document.getElementById('skip_button');
                if (skip) skip.click();
            }
        });
    </script>
</body>
</html>
