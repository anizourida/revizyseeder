<?php
use App\Models\Raiida\Page;

$grade5Id = 3;
$grade6Id = 2;

// 1. Clean up Grade 5 Duplicates
// Delete records with n_p_sem = 'FR_N5_P1_SEM3 _S2'
$deletedG5 = Page::where('grade_id', $grade5Id)
    ->where('n_p_sem', 'FR_N5_P1_SEM3 _S2')
    ->delete();
echo "Deleted {$deletedG5} duplicates from Grade 5.\n";

// 2. Clean up Grade 6 Duplicates
// Page 11: delete manual uploads
$deletedG6_11 = Page::where('grade_id', $grade6Id)
    ->where('page_number', '11')
    ->where('image_path', 'like', 'manual_uploads/%')
    ->delete();
echo "Deleted {$deletedG6_11} manual upload duplicates for Grade 6, page 11.\n";

// Page 39: delete duplicate from FR_N6_P2_SEM3_S2
$deletedG6_39 = Page::where('grade_id', $grade6Id)
    ->where('page_number', '39')
    ->where('n_p_sem', 'FR_N6_P2_SEM3_S2')
    ->delete();
echo "Deleted {$deletedG6_39} duplicates for Grade 6, page 39.\n";

// Page 22: find the wrong one and delete it.
$g5Page22 = Page::where('grade_id', $grade5Id)->where('page_number', '22')->first();
if ($g5Page22) {
    $deletedG6_22 = Page::where('grade_id', $grade6Id)
        ->where('page_number', '22')
        ->where('md5_checksum', '!=', $g5Page22->md5_checksum)
        ->delete();
    echo "Deleted {$deletedG6_22} wrong duplicates for Grade 6, page 22.\n";
}

// 3. Clone missing pages from Grade 5 to Grade 6
$g5Pages = Page::where('grade_id', $grade5Id)->whereNotNull('page_number')->get()->keyBy('page_number');
$g6Pages = Page::where('grade_id', $grade6Id)->whereNotNull('page_number')->get()->keyBy('page_number');

$missingIn6 = array_diff($g5Pages->keys()->toArray(), $g6Pages->keys()->toArray());

$clonedCount = 0;
foreach ($missingIn6 as $missingNum) {
    $sourcePage = $g5Pages[$missingNum];
    $newPage = $sourcePage->replicate();
    $newPage->grade_id = $grade6Id;
    $newPage->n_p_sem = str_replace('N5', 'N6', $newPage->n_p_sem);
    
    $oldPath = storage_path('app/public/' . $sourcePage->image_path);
    if (!file_exists($oldPath)) $oldPath = storage_path('app/' . $sourcePage->image_path);
    
    if (file_exists($oldPath)) {
        $newRelPath = str_replace('N5', 'N6', $sourcePage->image_path);
        $newAbsPath = storage_path('app/' . $newRelPath);
        
        if (!is_dir(dirname($newAbsPath))) {
            mkdir(dirname($newAbsPath), 0777, true);
        }
        
        copy($oldPath, $newAbsPath);
        $newPage->image_path = $newRelPath;
    } else {
        echo "Warning: Source file not found for page {$missingNum}: {$oldPath}\n";
    }
    
    $newPage->save();
    $clonedCount++;
}

echo "Cloned {$clonedCount} missing pages to Grade 6.\n";

// 4. Verification
$g5Count = Page::where('grade_id', $grade5Id)->whereNotNull('page_number')->distinct('page_number')->count();
$g6Count = Page::where('grade_id', $grade6Id)->whereNotNull('page_number')->distinct('page_number')->count();
echo "Verification: Grade 5 unique pages: {$g5Count}. Grade 6 unique pages: {$g6Count}.\n";

$g5Dups = \DB::table('pages')->where('grade_id', $grade5Id)->whereNotNull('page_number')->groupBy('page_number')->havingRaw('count(*) > 1')->count();
$g6Dups = \DB::table('pages')->where('grade_id', $grade6Id)->whereNotNull('page_number')->groupBy('page_number')->havingRaw('count(*) > 1')->count();
echo "Verification: Grade 5 duplicates: {$g5Dups}. Grade 6 duplicates: {$g6Dups}.\n";
