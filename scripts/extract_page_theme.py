import sys
import os
import cv2
import numpy as np
from sklearn.cluster import KMeans
import json

def get_main_page_color(image_path, k=4):
    if not os.path.exists(image_path):
        return {"status": "error", "message": "File not found"}
        
    # Load image
    img = cv2.imread(image_path)
    if img is None:
        return {"status": "error", "message": "Could not read image"}
        
    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    
    # Resize for speed
    img = cv2.resize(img, (400, 400))
    
    # Convert to HSV
    hsv = cv2.cvtColor(img, cv2.COLOR_RGB2HSV)
    h, s, v = cv2.split(hsv)
    
    # Normalize
    s = s / 255.0
    v = v / 255.0
    
    # --- FILTERS ---
    mask = (
        (s > 0.25) &          # remove low saturation (text, gray)
        (v > 0.15) &          # remove black
        ~((v > 0.9) & (s < 0.2))  # remove white
    )
    
    # --- SPATIAL FILTER (keep borders) ---
    h_img, w_img = mask.shape
    border_mask = np.zeros_like(mask)
    border_mask[:int(0.2*h_img), :] = 1
    border_mask[int(0.8*h_img):, :] = 1
    border_mask[:, :int(0.15*w_img)] = 1
    border_mask[:, int(0.85*w_img):] = 1
    
    final_mask = mask & border_mask
    
    # Extract pixels
    pixels = img[final_mask]
    if len(pixels) < 50:
        return {"status": "error", "message": "Not enough valid pixels detected"}
        
    # --- KMEANS ---
    kmeans = KMeans(n_clusters=k, n_init=10)
    labels = kmeans.fit_predict(pixels)
    centers = kmeans.cluster_centers_
    
    # Find dominant cluster
    counts = np.bincount(labels)
    dominant = centers[np.argmax(counts)]
    
    color_rgb = dominant.astype(int)
    hex_color = '#%02x%02x%02x' % tuple(color_rgb)
    
    return {
        "status": "success",
        "hex": hex_color,
        "rgb": color_rgb.tolist()
    }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"status": "error", "message": "Missing image path argument"}))
        sys.exit(1)
        
    result = get_main_page_color(sys.argv[1])
    # Print clean JSON for PHP script to parse
    print(json.dumps(result))
