import cv2
import tempfile
import subprocess

img = cv2.imread('debug_bottom.png')
h, w = img.shape[:2]

# Crop ONLY the outer 8% to isolate the badge
left_corner = img[:, :int(w*0.08)]
right_corner = img[:, int(w*0.92):]

for name, crop in [("Left_Corner", left_corner), ("Right_Corner", right_corner)]:
    gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
    resized = cv2.resize(gray, None, fx=4, fy=4, interpolation=cv2.INTER_CUBIC)
    _, thresh = cv2.threshold(resized, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
    
    cv2.imwrite(f'debug_{name}.png', thresh)
    inv = cv2.bitwise_not(thresh)
    cv2.imwrite(f'debug_{name}_inv.png', inv)
    
    with tempfile.NamedTemporaryFile(suffix='.png') as f:
        cv2.imwrite(f.name, inv)
        res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '8', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
        print(f"{name} INV PSM 8: {res.stdout.strip()}")
        
        cv2.imwrite(f.name, thresh)
        res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '8', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
        print(f"{name} THR PSM 8: {res.stdout.strip()}")
