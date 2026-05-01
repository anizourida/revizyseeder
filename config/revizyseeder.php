<?php

return [
    'dashboard' => [
        // Python OCR (tesseract) is heuristic and often unreliable for these slides.
        // Keep it available as a fallback, but default to off.
        'enable_python_ocr' => (bool) env('REVIZYSEEDER_ENABLE_PYTHON_OCR', false),
        'enable_lmstudio_ocr' => (bool) env('REVIZYSEEDER_ENABLE_LMSTUDIO_OCR', true),
    ],

    'page_number' => [
        // Heuristic range used to flag "likely wrong" page numbers that came from OCR.
        // This matches the earlier `< 10` logic used in the extractor job.
        'suspicious_min' => (int) env('REVIZYSEEDER_SUSPICIOUS_PAGE_NUMBER_MIN', 1),
        'suspicious_max' => (int) env('REVIZYSEEDER_SUSPICIOUS_PAGE_NUMBER_MAX', 9),
    ],
];

