import cv2
import tempfile
import subprocess
import re

img = cv2.imread('debug_bottom.png')
h, w = img.shape[:2]

hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
_, s, _ = cv2.split(hsv)

s_inv = cv2.bitwise_not(s)
_, thresh = cv2.threshold(s_inv, 200, 255, cv2.THRESH_BINARY)

resized = cv2.resize(thresh, None, fx=3, fy=3, interpolation=cv2.INTER_CUBIC)
inv = cv2.bitwise_not(resized)

with tempfile.NamedTemporaryFile(suffix='.png') as f:
    cv2.imwrite(f.name, inv)
    res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '11', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
    
    print("Detected numbers:")
    nums = re.findall(r'\d+', res.stdout)
    print(nums)
