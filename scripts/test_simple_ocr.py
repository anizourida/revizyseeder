import cv2
import tempfile
import subprocess
import re

img = cv2.imread('debug_bottom.png')
h, w = img.shape[:2]

left_crop = img[:, :int(w*0.15)]
right_crop = img[:, int(w*0.85):]

for name, crop in [("Left", left_crop), ("Right", right_crop)]:
    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
    resized = cv2.resize(gray, None, fx=4, fy=4, interpolation=cv2.INTER_CUBIC)
    _, thresh = cv2.threshold(resized, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
    
    # White text on dark badge means text is 255. We want black text on white background for Tesseract.
    # So we invert.
    inv = cv2.bitwise_not(thresh)
    
    cv2.imwrite(f'debug_{name}.png', inv)
    
    with tempfile.NamedTemporaryFile(suffix='.png') as f:
        cv2.imwrite(f.name, inv)
        res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '8', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
        print(f"{name} PSM 8: {res.stdout.strip()}")
        
        res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '7', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
        print(f"{name} PSM 7: {res.stdout.strip()}")
        
        # Also try without inversion in case it's black text on light background
        cv2.imwrite(f.name, thresh)
        res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '8', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
        print(f"{name} PSM 8 (no inv): {res.stdout.strip()}")
