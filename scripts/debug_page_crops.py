"""Debug: try extreme corner crops to isolate just the page number badge."""
import cv2
import sys
import os
import subprocess

image_path = sys.argv[1]
img = cv2.imread(image_path)
h, w = img.shape[:2]
print(f"Image: {w}x{h}")

out_dir = "scripts/debug_crops"
os.makedirs(out_dir, exist_ok=True)

# The badge is typically in the very extreme corner, approx 50x50px area
# Try tight crops of just the corner badge
configs = [
    ("tight", 0.08, 0.15),
    ("xtight", 0.06, 0.12),
]

for label, strip_pct, corner_pct in configs:
    strip_h = int(h * strip_pct)
    corner_w = int(w * corner_pct)
    
    for side, crop in [
        ("right", img[h - strip_h:h, w - corner_w:w]),
        ("left", img[h - strip_h:h, 0:corner_w])
    ]:
        gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
        # Massive upscale for tiny crops
        upscaled = cv2.resize(gray, None, fx=6, fy=6, interpolation=cv2.INTER_CUBIC)
        _, otsu = cv2.threshold(upscaled, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        inverted = cv2.bitwise_not(otsu)
        
        fname = f"{out_dir}/{label}_{side}.png"
        cv2.imwrite(fname, inverted)
        
        for psm in [6, 7, 8, 13, 10]:
            result = subprocess.run(
                ['tesseract', fname, 'stdout', '--psm', str(psm),
                 '-c', 'tessedit_char_whitelist=0123456789'],
                stdout=subprocess.PIPE, stderr=subprocess.DEVNULL, text=True
            )
            text = result.stdout.strip()
            if text:
                print(f"  [{label}_{side}] PSM={psm}: '{text}'")
        
        # Without whitelist
        for psm in [6, 7, 8]:
            result = subprocess.run(
                ['tesseract', fname, 'stdout', '--psm', str(psm)],
                stdout=subprocess.PIPE, stderr=subprocess.DEVNULL, text=True
            )
            text = result.stdout.strip()
            if text:
                print(f"  [{label}_{side}] PSM={psm} (no wl): '{text}'")
