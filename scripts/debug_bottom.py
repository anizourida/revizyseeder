import cv2
import sys
import os

image_path = sys.argv[1]
img = cv2.imread(image_path)
h, w = img.shape[:2]

# Crop bottom 15%
bottom_crop = img[int(h*0.85):h, 0:w]

out_path = 'debug_bottom.png'
cv2.imwrite(out_path, bottom_crop)
print(f"Saved bottom 15% to {out_path}")
