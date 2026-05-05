"""
Simple Bottom-Edge Page Extractor v10.0
- ONLY looks at the bottom 12% of the page (to avoid question numbers).
- ONLY accepts 2-digit and 3-digit numbers (10-999).
- Simple Otsu thresholding + Tesseract.
"""

import cv2
import sys
import os
import subprocess
import tempfile
import re
from collections import Counter

def run_tesseract(img, psm=7):
    with tempfile.NamedTemporaryFile(suffix='.png', delete=False) as f:
        temp_path = f.name
        cv2.imwrite(temp_path, img)
    try:
        result = subprocess.run(
            ['tesseract', temp_path, 'stdout', '--psm', str(psm), 
             '-c', 'tessedit_char_whitelist=0123456789'],
            stdout=subprocess.PIPE, stderr=subprocess.DEVNULL, text=True
        )
        return result.stdout.strip()
    finally:
        if os.path.exists(temp_path):
            os.remove(temp_path)

def simple_ocr(crop):
    candidates = []
    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
    
    # Simple contrast boost
    clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
    cl1 = clahe.apply(gray)

    # Upscale for better OCR
    for scale in [3, 4]:
        resized = cv2.resize(cl1, None, fx=scale, fy=scale, interpolation=cv2.INTER_CUBIC)
        
        # Standard thresholding
        _, t1 = cv2.threshold(resized, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        
        # Test both regular and inverted (in case badge is dark)
        for t in [t1, cv2.bitwise_not(t1)]:
            # PSM 7: Treat as a single line of text
            # PSM 8: Treat as a single word
            for psm in [7, 8]:
                text = run_tesseract(t, psm)
                for n in re.findall(r'\d+', text):
                    # STRICT 2-3 DIGIT FILTER
                    if 2 <= len(n) <= 3:
                        candidates.append(int(n))

    return candidates

def extract_page_number(image_path):
    if not os.path.exists(image_path):
        print("|none|file_not_found", end="")
        return

    img = cv2.imread(image_path)
    if img is None:
        print("|none|error", end="")
        return

    h, w = img.shape[:2]
    all_votes = []
    
    # SIMPLE CROP: Bottom 12% of height, 20% of width corners
    sh, cw = int(h * 0.12), int(w * 0.20)
    
    # Right Corner
    votes_r = simple_ocr(img[h-sh:h, w-cw:w])
    all_votes.extend([(v, 'right') for v in votes_r])
    
    # Left Corner
    votes_l = simple_ocr(img[h-sh:h, 0:cw])
    all_votes.extend([(v, 'left') for v in votes_l])

    if not all_votes:
        print("|none|no_number", end="")
        return

    counts = Counter([v[0] for v in all_votes])
    best_num, count = counts.most_common(1)[0]
    
    side_counts = Counter([v[1] for v in all_votes if v[0] == best_num])
    best_side = side_counts.most_common(1)[0][0]

    # Confidence: Need at least 2 matches since we are strictly looking at the bottom corners
    if count >= 3:
        conf = 'high'
    elif count >= 2:
        conf = 'medium'
    else:
        conf = 'low'

    print(f"{best_num}|{conf}|bottom_{best_side}", end="")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        extract_page_number(sys.argv[1])
    else:
        print("|none|no_args", end="")
