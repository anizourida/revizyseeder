import cv2
import tempfile
import subprocess

img = cv2.imread('debug_bottom.png')

# Convert to HSV
hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
h, s, v = cv2.split(hsv)

# White text has low saturation and high value
# Let's threshold the Saturation channel
# Invert S: low saturation becomes high (white)
s_inv = cv2.bitwise_not(s)

# Threshold to get only the whitest parts (text)
_, thresh = cv2.threshold(s_inv, 200, 255, cv2.THRESH_BINARY)

cv2.imwrite('debug_s_inv.png', s_inv)
cv2.imwrite('debug_thresh.png', thresh)

with tempfile.NamedTemporaryFile(suffix='.png') as f:
    cv2.imwrite(f.name, thresh)
    res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '11', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
    print("PSM 11 on saturation threshold:")
    print(res.stdout)
    
    # We want Tesseract to see black text on white background
    # Our thresh is white text on black background
    inv = cv2.bitwise_not(thresh)
    cv2.imwrite('debug_thresh_inv.png', inv)
    cv2.imwrite(f.name, inv)
    res = subprocess.run(['tesseract', f.name, 'stdout', '--psm', '11', '-c', 'tessedit_char_whitelist=0123456789'], capture_output=True, text=True)
    print("PSM 11 on inverted saturation threshold:")
    print(res.stdout)
