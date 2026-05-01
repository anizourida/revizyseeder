import cv2
import sys
import os
import subprocess
import tempfile
import re

def extract_from_crop(img_crop):
    # preprocess
    gray = cv2.cvtColor(img_crop, cv2.COLOR_BGR2GRAY)
    gray = cv2.resize(gray, None, fx=2, fy=2, interpolation=cv2.INTER_CUBIC)
    _, thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Save to temp file to pass to tesseract binary
    with tempfile.NamedTemporaryFile(suffix='.png', delete=False) as temp_img:
        temp_img_path = temp_img.name
        cv2.imwrite(temp_img_path, thresh)

    try:
        # Call tesseract
        process = subprocess.run(
            ['tesseract', temp_img_path, 'stdout', '--psm', '6'],
            stdout=subprocess.PIPE,
            stderr=subprocess.DEVNULL,
            text=True
        )
        text = process.stdout
    finally:
        if os.path.exists(temp_img_path):
            os.remove(temp_img_path)
    
    tokens = re.findall(r'\d+', text)
    valid_numbers = [t for t in tokens if 1 <= len(t) <= 3]
    return valid_numbers[-1] if valid_numbers else ""

def extract_page_number(image_path):
    if not os.path.exists(image_path):
        return

    # load image
    img = cv2.imread(image_path)
    if img is None:
        return

    height, width = img.shape[:2]
    crop_h = int(height * 0.12)
    crop_w = int(width * 0.25) # Check 25% of the width on each side

    # Corner 1: Bottom Right
    bottom_right = img[height - crop_h : height, width - crop_w : width]
    page_number = extract_from_crop(bottom_right)

    # Corner 2: Bottom Left (Fallback)
    if not page_number:
        bottom_left = img[height - crop_h : height, 0 : crop_w]
        page_number = extract_from_crop(bottom_left)
        
    print(page_number, end="")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        extract_page_number(sys.argv[1])
