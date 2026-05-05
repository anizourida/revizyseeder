import cv2
import os
import sys

def debug_crops(image_path):
    img = cv2.imread(image_path)
    h, w = img.shape[:2]
    
    # Same crops as the main script
    crop_configs = [
        (0.06, 0.12, 'xtight'),
        (0.08, 0.15, 'tight'),
        (0.10, 0.20, 'standard')
    ]

    os.makedirs('debug_ocr', exist_ok=True)

    for sh_pct, cw_pct, label in crop_configs:
        sh, cw = int(h * sh_pct), int(w * cw_pct)
        
        # Save Right
        right = img[h-sh:h, w-cw:w]
        cv2.imwrite(f'debug_ocr/{label}_right.png', right)
        
        # Save Left
        left = img[h-sh:h, 0:cw]
        cv2.imwrite(f'debug_ocr/{label}_left.png', left)

if __name__ == "__main__":
    debug_crops(sys.argv[1])
