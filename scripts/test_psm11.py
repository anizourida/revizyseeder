import cv2
import tempfile
import subprocess
import re

img = cv2.imread('debug_bottom.png')

# Just grayscale, no thresholding
gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
# scale up a bit
resized = cv2.resize(gray, None, fx=2, fy=2, interpolation=cv2.INTER_CUBIC)

with tempfile.NamedTemporaryFile(suffix='.png') as f:
    cv2.imwrite(f.name, resized)
    
    # Run tesseract with PSM 11 (sparse text)
    res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '11', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
    print("PSM 11 output:")
    print(res.stdout)
