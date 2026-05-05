import cv2
import tempfile
import subprocess
import re

img = cv2.imread('debug_bottom.png')
h, w = img.shape[:2]

# Crop corners to isolate badges
left = img[:, :int(w*0.15)]
right = img[:, int(w*0.85):]

for name, crop in [("Left", left), ("Right", right)]:
    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
    
    # Adaptive threshold
    thresh = cv2.adaptiveThreshold(gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY_INV, 11, 2)
    
    # Find contours
    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    
    for c in contours:
        x, y, cw, ch = cv2.boundingRect(c)
        if 10 < cw < 100 and 10 < ch < 100:
            # Extract
            digit_crop = thresh[y:y+ch, x:x+cw]
            # Pad
            digit_crop = cv2.copyMakeBorder(digit_crop, 10, 10, 10, 10, cv2.BORDER_CONSTANT, value=0)
            
            # Resize
            resized = cv2.resize(digit_crop, None, fx=4, fy=4, interpolation=cv2.INTER_CUBIC)
            
            # Invert so it's black on white
            inv = cv2.bitwise_not(resized)
            
            with tempfile.NamedTemporaryFile(suffix='.png') as f:
                cv2.imwrite(f.name, inv)
                res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '10', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
                val = res.stdout.strip()
                if val:
                    print(f"{name} found contour text: {val}")
