<?php
use App\Models\Raiida\Page;
use App\Models\Raiida\Grade;

$grades = Grade::all()->keyBy('id');

// 1. Check for page number duplication per grade
$duplicates = \DB::table('pages')
    ->select('grade_id', 'page_number', \DB::raw('count(*) as total'))
    ->whereNotNull('page_number')
    ->groupBy('grade_id', 'page_number')
    ->having('total', '>', 1)
    ->get();

$duplicateReport = [];
foreach ($duplicates as $dup) {
    $gradeName = isset($grades[$dup->grade_id]) ? $grades[$dup->grade_id]->name : 'Unknown';
    if (!isset($duplicateReport[$gradeName])) {
        $duplicateReport[$gradeName] = [];
    }
    $duplicateReport[$gradeName][] = [
        'page_number' => $dup->page_number,
        'count' => $dup->total
    ];
}

// 2. Compare 5th grade and 6th grade pages
$grade5Id = 3; // Name is "5"
$grade6Id = 2; // Name is "6"

$grade5Pages = Page::where('grade_id', $grade5Id)->whereNotNull('page_number')->get()->keyBy('page_number');
$grade6Pages = Page::where('grade_id', $grade6Id)->whereNotNull('page_number')->get()->keyBy('page_number');

$g5Numbers = $grade5Pages->keys()->toArray();
$g6Numbers = $grade6Pages->keys()->toArray();

$missingIn5 = array_diff($g6Numbers, $g5Numbers);
$missingIn6 = array_diff($g5Numbers, $g6Numbers);

// Check if content (checksum) is identical for matching page numbers
$mismatchedContent = [];
$commonNumbers = array_intersect($g5Numbers, $g6Numbers);
foreach ($commonNumbers as $pageNum) {
    $g5Page = $grade5Pages[$pageNum];
    $g6Page = $grade6Pages[$pageNum];
    
    if ($g5Page->md5_checksum !== $g6Page->md5_checksum) {
        $mismatchedContent[] = $pageNum;
    }
}

// Generate Markdown Content
$md = "# Page Analysis Report\n\n";

$md .= "## 1. Page Number Duplication per Grade\n\n";
if (empty($duplicateReport)) {
    $md .= "> [!TIP]\n> No page number duplications found in any grade.\n\n";
} else {
    $md .= "> [!WARNING]\n> Found page number duplications!\n\n";
    foreach ($duplicateReport as $gradeName => $dups) {
        $md .= "### Grade: {$gradeName}\n";
        $md .= "| Page Number | Count |\n";
        $md .= "|-------------|-------|\n";
        foreach ($dups as $dup) {
            $md .= "| {$dup['page_number']} | {$dup['count']} |\n";
        }
        $md .= "\n";
    }
}

$md .= "## 2. Grade 5 vs Grade 6 Pages Comparison\n\n";
$md .= "- **Total Pages in Grade 5**: " . count($g5Numbers) . "\n";
$md .= "- **Total Pages in Grade 6**: " . count($g6Numbers) . "\n\n";

$md .= "### Missing Pages\n";
if (empty($missingIn5) && empty($missingIn6)) {
    $md .= "> [!TIP]\n> Grade 5 and Grade 6 have the exact same set of page numbers.\n\n";
} else {
    $md .= "#### Pages in Grade 6 but missing in Grade 5:\n";
    $md .= empty($missingIn5) ? "None\n\n" : implode(', ', $missingIn5) . "\n\n";

    $md .= "#### Pages in Grade 5 but missing in Grade 6:\n";
    $md .= empty($missingIn6) ? "None\n\n" : implode(', ', $missingIn6) . "\n\n";
}

$md .= "### Content Mismatch Check\n";
$md .= "Comparing the `md5_checksum` for pages that exist in both grades:\n\n";
if (empty($mismatchedContent)) {
    $md .= "> [!TIP]\n> All common pages between Grade 5 and Grade 6 have identical images/content.\n\n";
} else {
    $md .= "> [!WARNING]\n> The following pages have the same page number in both grades, but their image content (`md5_checksum`) is **different**:\n\n";
    $md .= implode(', ', $mismatchedContent) . "\n";
}

$artifactPath = '/Users/macbook/.gemini/antigravity/brain/2d032d6b-1987-439f-9ef8-50003e8a1ccd/artifacts/page_analysis_report.md';
if (!is_dir(dirname($artifactPath))) {
    mkdir(dirname($artifactPath), 0777, true);
}
file_put_contents($artifactPath, $md);

echo "Report generated at: " . $artifactPath . "\n";
