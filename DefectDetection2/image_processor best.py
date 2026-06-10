import cv2
import numpy as np
from scipy import ndimage
import os

class ImageProcessor:
    def __init__(self):
        self.yolo_net = None
        self.classes = []
        self.output_layers = []
        self.load_yolo_model()
    
    def load_yolo_model(self):
        """Load YOLOv4-Tiny model for defect detection"""
        try:
            # Paths to YOLO files
            weights_path = 'models/yolov4-tiny.weights'
            cfg_path = 'models/yolov4-tiny.cfg'
            names_path = 'models/coco.names'
            
            # Download if files don't exist
            if not os.path.exists(weights_path):
                print("Downloading YOLOv4-tiny weights...")
                self.download_yolo_weights()
            
            if os.path.exists(weights_path) and os.path.exists(cfg_path):
                # Load YOLO network
                self.yolo_net = cv2.dnn.readNet(weights_path, cfg_path)
                
                # Get output layer names
                layer_names = self.yolo_net.getLayerNames()
                self.output_layers = [layer_names[i - 1] for i in self.yolo_net.getUnconnectedOutLayers()]
                
                # Load COCO class names (for general object detection)
                if os.path.exists(names_path):
                    with open(names_path, 'r') as f:
                        self.classes = [line.strip() for line in f.readlines()]
                else:
                    # Default classes - you should train a custom model for defects
                    self.classes = ['crack', 'dent', 'corrosion', 'spalling', 'leak']
                
                # Use GPU if available
                self.yolo_net.setPreferableBackend(cv2.dnn.DNN_BACKEND_OPENCV)
                self.yolo_net.setPreferableTarget(cv2.dnn.DNN_TARGET_CPU)
                
                print("YOLOv4-Tiny model loaded successfully!")
            else:
                print("YOLO model files not found. Using classical methods only.")
                
        except Exception as e:
            print(f"YOLO model loading failed: {e}. Using classical methods only.")
    
    def download_yolo_weights(self):
        """Download YOLOv4-tiny weights and configuration"""
        import urllib.request
        
        # Create models directory
        os.makedirs('models', exist_ok=True)
        
        # Download YOLOv4-tiny files
        yolo_files = {
            'yolov4-tiny.weights': 'https://github.com/AlexeyAB/darknet/releases/download/darknet_yolo_v4_pre/yolov4-tiny.weights',
            'yolov4-tiny.cfg': 'https://raw.githubusercontent.com/AlexeyAB/darknet/master/cfg/yolov4-tiny.cfg',
            'coco.names': 'https://raw.githubusercontent.com/pjreddie/darknet/master/data/coco.names'
        }
        
        for filename, url in yolo_files.items():
            filepath = f'models/{filename}'
            if not os.path.exists(filepath):
                print(f"Downloading {filename}...")
                try:
                    urllib.request.urlretrieve(url, filepath)
                    print(f"Downloaded {filename} successfully!")
                except Exception as e:
                    print(f"Failed to download {filename}: {e}")
    
    def preprocess_image(self, image):
        """Preprocess image for analysis"""
        # Convert to grayscale
        if len(image.shape) == 3:
            gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        else:
            gray = image.copy()
        
        # Resize image for consistent processing
        height, width = gray.shape
        if max(height, width) > 800:
            scale = 800 / max(height, width)
            new_width = int(width * scale)
            new_height = int(height * scale)
            gray = cv2.resize(gray, (new_width, new_height))
        
        # Noise removal
        denoised = cv2.medianBlur(gray, 5)
        
        # Contrast enhancement
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
        enhanced = clahe.apply(denoised)
        
        return enhanced
    
    def detect_defects_classical(self, image):
        """Detect defects using classical image processing techniques"""
        processed = self.preprocess_image(image)
        defects = []
        
        # Edge detection for crack detection
        edges = cv2.Canny(processed, 50, 150)
        
        # Thresholding for defect segmentation
        _, binary = cv2.threshold(processed, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        
        # Morphological operations to clean up the image
        kernel = np.ones((3,3), np.uint8)
        binary_cleaned = cv2.morphologyEx(binary, cv2.MORPH_CLOSE, kernel)
        
        # Find contours
        contours, _ = cv2.findContours(edges, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
        
        # Filter contours based on area and shape
        min_area = 50
        max_area = 5000
        
        for contour in contours:
            area = cv2.contourArea(contour)
            if min_area < area < max_area:
                # Calculate contour properties
                perimeter = cv2.arcLength(contour, True)
                if perimeter == 0:
                    continue
                
                circularity = 4 * np.pi * area / (perimeter * perimeter)
                
                # Classify based on shape properties
                if circularity < 0.3:  # Likely crack (elongated)
                    defects.append({
                        'type': 'crack', 
                        'contour': contour,
                        'confidence': 0.7,
                        'area': float(area)  # Convert to float for JSON
                    })
                elif 0.3 <= circularity <= 0.7:  # Irregular shape
                    defects.append({
                        'type': 'irregularity', 
                        'contour': contour,
                        'confidence': 0.6,
                        'area': float(area)
                    })
                else:  # Circular shape - possible corrosion spot
                    defects.append({
                        'type': 'corrosion', 
                        'contour': contour,
                        'confidence': 0.5,
                        'area': float(area)
                    })
        
        return defects
    
    def detect_defects_yolo(self, image):
        """Detect defects using YOLOv4-Tiny with proper implementation"""
        if self.yolo_net is None:
            return []
        
        height, width = image.shape[:2]
        
        # Prepare image for YOLO
        blob = cv2.dnn.blobFromImage(
            image, 
            1/255.0, 
            (416, 416), 
            swapRB=True, 
            crop=False
        )
        self.yolo_net.setInput(blob)
        
        # Run inference
        outputs = self.yolo_net.forward(self.output_layers)
        
        # Initialize lists for detection results
        boxes = []
        confidences = []
        class_ids = []
        defects = []
        
        conf_threshold = 0.5
        nms_threshold = 0.4
        
        # Process each output layer
        for output in outputs:
            for detection in output:
                scores = detection[5:]
                class_id = np.argmax(scores)
                confidence = scores[class_id]
                
                if confidence > conf_threshold:
                    # Scale bounding box coordinates to original image size
                    center_x = int(detection[0] * width)
                    center_y = int(detection[1] * height)
                    w = int(detection[2] * width)
                    h = int(detection[3] * height)
                    
                    # Calculate coordinates
                    x = int(center_x - w/2)
                    y = int(center_y - h/2)
                    
                    boxes.append([x, y, w, h])
                    confidences.append(float(confidence))
                    class_ids.append(class_id)
        
        # Apply Non-Maximum Suppression
        indices = cv2.dnn.NMSBoxes(boxes, confidences, conf_threshold, nms_threshold)
        
        if len(indices) > 0:
            for i in indices.flatten():
                x, y, w, h = boxes[i]
                
                # Get class name
                class_name = self.classes[class_ids[i]] if class_ids[i] < len(self.classes) else 'unknown'
                
                # Filter for potential defects (you can customize this)
                if self.is_potential_defect(class_name, confidences[i]):
                    defects.append({
                        'type': class_name,
                        'confidence': float(confidences[i]),  # Convert to float
                        'bbox': (x, y, w, h),
                        'area': float(w * h)  # Convert to float
                    })
        
        return defects
    
    def is_potential_defect(self, class_name, confidence):
        """Filter objects that could be relevant for defect detection"""
        # COCO classes that might indicate defects or damage
        defect_related_classes = [
            'person', 'bicycle', 'car', 'motorcycle', 'airplane', 'bus', 'train', 
            'truck', 'boat', 'traffic light', 'fire hydrant', 'stop sign', 
            'parking meter', 'bench', 'bird', 'cat', 'dog', 'horse', 'sheep', 
            'cow', 'elephant', 'bear', 'zebra', 'giraffe', 'backpack', 'umbrella',
            'handbag', 'tie', 'suitcase', 'frisbee', 'skis', 'snowboard',
            'sports ball', 'kite', 'baseball bat', 'baseball glove', 'skateboard',
            'surfboard', 'tennis racket', 'bottle', 'wine glass', 'cup', 'fork',
            'knife', 'spoon', 'bowl', 'banana', 'apple', 'sandwich', 'orange',
            'broccoli', 'carrot', 'hot dog', 'pizza', 'donut', 'cake', 'chair',
            'couch', 'potted plant', 'bed', 'dining table', 'toilet', 'tv',
            'laptop', 'mouse', 'remote', 'keyboard', 'cell phone', 'microwave',
            'oven', 'toaster', 'sink', 'refrigerator', 'book', 'clock', 'vase',
            'scissors', 'teddy bear', 'hair drier', 'toothbrush'
        ]
        
        # For demo purposes, we'll consider certain objects as potential "defects"
        # In a real scenario, you should train a custom YOLO model on defect data
        potential_defect_objects = ['crack', 'dent', 'corrosion', 'spalling', 'leak']
        
        return (class_name in potential_defect_objects or 
                confidence > 0.7)  # High confidence detections
    
    def detect_defects_custom_model(self, image):
        """Placeholder for custom trained defect detection model"""
        # This is where you would integrate a custom YOLO model
        # trained specifically on defect datasets
        
        # For now, return empty list - implement with your custom model
        return []
    
    def nms_multimethod(self, defects):
        """Apply NMS across defects from different detection methods"""
        if not defects:
            return []
        
        boxes = []
        scores = []
        valid_defects = []
        
        for defect in defects:
            if 'bbox' in defect:
                x, y, w, h = defect['bbox']
                boxes.append([x, y, x + w, y + h])
                scores.append(defect.get('confidence', 0.5))
                valid_defects.append(defect)
        
        if not boxes:
            return defects
        
        # Convert to numpy arrays
        boxes = np.array(boxes)
        scores = np.array(scores)
        
        # Apply NMS
        indices = cv2.dnn.NMSBoxes(
            [b.tolist() for b in boxes], 
            scores.tolist(), 
            0.5,  # confidence threshold
            0.4   # NMS threshold
        )
        
        if len(indices) > 0:
            return [valid_defects[i] for i in indices.flatten()]
        else:
            return valid_defects
    
    def detect_defects(self, image_path):
        """Main defect detection function - JSON serializable version"""
        # Read image
        image = cv2.imread(image_path)
        if image is None:
            raise ValueError("Could not read image")
        
        # Create a copy for drawing results
        result_image = image.copy()
        
        print("Starting defect detection...")
        
        # Step 1: Classical image processing for defect detection
        print("Running classical defect detection...")
        classical_defects = self.detect_defects_classical(image)
        
        # Step 2: YOLOv4-Tiny object detection
        print("Running YOLOv4-Tiny detection...")
        yolo_defects = self.detect_defects_yolo(image)
        
        # Step 3: Custom defect detection (if available)
        print("Running custom defect detection...")
        custom_defects = self.detect_defects_custom_model(image)
        
        # Combine all detection methods
        all_defects = classical_defects + yolo_defects + custom_defects
        
        print(f"Classical: {len(classical_defects)}, YOLO: {len(yolo_defects)}, Custom: {len(custom_defects)}")
        
        # Apply multi-method NMS to remove duplicates
        filtered_defects = self.nms_multimethod(all_defects)
        
        # Convert defects to JSON-serializable format
        serializable_defects = []
        defect_types = {}
        
        for i, defect in enumerate(filtered_defects):
            defect_type = defect['type']
            
            # Count defect types
            defect_types[defect_type] = defect_types.get(defect_type, 0) + 1
            
            # Create serializable defect object
            serializable_defect = {
                'id': i + 1,
                'type': defect_type,
                'confidence': float(defect.get('confidence', 0.5)),
                'area': float(defect.get('area', 0))
            }
            
            # Handle bounding box
            if 'bbox' in defect:
                x, y, w, h = defect['bbox']
                serializable_defect['bbox'] = {
                    'x': int(x),
                    'y': int(y),
                    'width': int(w),
                    'height': int(h)
                }
                # Draw bounding box (YOLO detection)
                color = (0, 255, 0)  # Green for YOLO
                cv2.rectangle(result_image, (x, y), (x+w, y+h), color, 3)
                
                # Add label
                label = f"{defect_type} (YOLO) {defect.get('confidence', 0):.2f}"
                cv2.putText(result_image, label, (x, y-10),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.6, color, 2)
            
            # Handle contour
            elif 'contour' in defect:
                contour = defect['contour']
                # Convert contour to list for serialization
                serializable_defect['contour_points'] = contour.reshape(-1, 2).tolist()
                
                # Calculate bounding rectangle for visualization
                x, y, w, h = cv2.boundingRect(contour)
                serializable_defect['bbox'] = {
                    'x': int(x),
                    'y': int(y),
                    'width': int(w),
                    'height': int(h)
                }
                
                # Draw contour (Classical detection)
                color = (0, 0, 255)  # Red for classical
                cv2.drawContours(result_image, [contour], -1, color, 3)
                
                # Add text near contour
                M = cv2.moments(contour)
                if M["m00"] != 0:
                    cx = int(M["m10"] / M["m00"])
                    cy = int(M["m01"] / M["m00"])
                    cv2.putText(result_image, f"{defect_type} (Classical)", 
                            (cx, cy), cv2.FONT_HERSHEY_SIMPLEX, 0.6, color, 2)
            
            serializable_defects.append(serializable_defect)
        
        # Add detection summary to image
        summary_text = f"Defects: {len(filtered_defects)}"
        cv2.putText(result_image, summary_text, (10, 30), 
                cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2)
        
        # Return JSON-serializable result
        return {
            'defect_count': len(filtered_defects),
            'defect_types': defect_types,
            'processed_image': result_image,
            'defects': serializable_defects,  # Use serializable defects
            'original_shape': {
                'height': int(image.shape[0]),
                'width': int(image.shape[1]),
                'channels': int(image.shape[2]) if len(image.shape) > 2 else 1
            },
            'detection_breakdown': {
                'classical': len(classical_defects),
                'yolo': len(yolo_defects),
                'custom': len(custom_defects)
            }
        }