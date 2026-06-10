# detector.py
import os
import cv2
import numpy as np
from skimage.morphology import skeletonize
import math

class ImageProcessor:
    def __init__(self,
                 yolo_cfg=None,
                 yolo_weights=None,
                 yolo_names=None,
                 yolo_input_size=640,
                 yolo_conf=0.4,
                 yolo_nms=0.45,
                 use_cuda=False):
        # classical params (tune if needed)
        self.min_contour_area = 80          # minimum contour area to consider
        self.min_crack_length_px = 20       # skeleton length threshold
        self.max_contour_area = 2000000     # upper area cap (avoid huge blanks)
        self.aspect_ratio_thresh = 2.0      # elongated shapes considered cracks
        self.skeleton_prune = 2             # optional prune parameter
        # YOLO config
        self.net = None
        self.class_names = []
        self.yolo_loaded = False
        if yolo_cfg and yolo_weights and yolo_names:
            try:
                self.net = cv2.dnn.readNet(yolo_weights, yolo_cfg)
                if use_cuda:
                    try:
                        self.net.setPreferableBackend(cv2.dnn.DNN_BACKEND_CUDA)
                        self.net.setPreferableTarget(cv2.dnn.DNN_TARGET_CUDA_FP16)
                    except Exception:
                        self.net.setPreferableBackend(cv2.dnn.DNN_BACKEND_OPENCV)
                        self.net.setPreferableTarget(cv2.dnn.DNN_TARGET_CPU)
                else:
                    self.net.setPreferableBackend(cv2.dnn.DNN_BACKEND_OPENCV)
                    self.net.setPreferableTarget(cv2.dnn.DNN_TARGET_CPU)
                with open(yolo_names, 'r') as f:
                    self.class_names = [l.strip() for l in f if l.strip()]
                self.yolo_input_size = yolo_input_size
                self.yolo_conf = yolo_conf
                self.yolo_nms = yolo_nms
                self.output_layer_names = self.net.getUnconnectedOutLayersNames()
                self.yolo_loaded = True
                print("[INFO] YOLO model loaded.")
            except Exception as e:
                print("[WARN] YOLO load failed:", e, "Falling back to classical detector.")

    # ---------- CLASSICAL PIPELINE ----------
    def preprocess(self, img, max_dim=1600):
        # keep original color copy for annotation
        color = img.copy()
        h, w = img.shape[:2]
        # scale down large images for speed but keep proportions
        if max(h, w) > max_dim:
            scale = max_dim / max(h, w)
            img = cv2.resize(img, (int(w*scale), int(h*scale)), interpolation=cv2.INTER_AREA)
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        # denoise while preserving edges
        gray = cv2.bilateralFilter(gray, d=9, sigmaColor=75, sigmaSpace=75)
        # improve local contrast
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8,8))
        gray = clahe.apply(gray)
        return img, color, gray

    def classical_detect(self, img):
        """
        Classical detection using adaptive threshold, morphological processing, skeletonization
        and geometric filtering to reduce false positives.
        Returns list of defects with bbox + contour + type='crack'/'spot'
        """
        img_resized, color, gray = self.preprocess(img)
        h, w = gray.shape
        # Adaptive threshold (better for varying illumination)
        binary = cv2.adaptiveThreshold(gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                                       cv2.THRESH_BINARY_INV, 25, 9)
        # Morphological opening to remove noise then closing to connect lines
        kernel_open = cv2.getStructuringElement(cv2.MORPH_RECT, (3,3))
        kernel_close = cv2.getStructuringElement(cv2.MORPH_RECT, (3,9))
        binary = cv2.morphologyEx(binary, cv2.MORPH_OPEN, kernel_open, iterations=1)
        binary = cv2.morphologyEx(binary, cv2.MORPH_CLOSE, kernel_close, iterations=1)
        # Optional: remove small blobs
        num_labels, labels, stats, centroids = cv2.connectedComponentsWithStats(binary, connectivity=8)
        cleaned = np.zeros_like(binary)
        for i in range(1, num_labels):
            area = stats[i, cv2.CC_STAT_AREA]
            if area >= self.min_contour_area:
                cleaned[labels == i] = 255

        # skeletonize (requires boolean image)
        ske = skeletonize(cleaned // 255).astype(np.uint8) * 255

        # find contours on skeleton dilated slightly
        dil = cv2.dilate(ske, cv2.getStructuringElement(cv2.MORPH_RECT, (3,3)), iterations=1)
        contours, _ = cv2.findContours(dil, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

        defects = []
        for cnt in contours:
            area = cv2.contourArea(cnt)
            if area < self.min_contour_area or area > self.max_contour_area:
                continue
            x,y,w_box,h_box = cv2.boundingRect(cnt)
            aspect = max(w_box/h_box if h_box else 0, h_box/w_box if w_box else 0)
            # compute skeleton length as a more reliable measure for cracks
            x0,y0,w0,h0 = x, y, w_box, h_box
            roi_ske = ske[y0:y0+h0, x0:x0+w0]
            # count skeleton pixels -> length proxy
            length = int(np.count_nonzero(roi_ske))
            # solidity
            hull = cv2.convexHull(cnt)
            hull_area = cv2.contourArea(hull) if hull is not None else area
            solidity = float(area) / hull_area if hull_area > 0 else 0
            # decide type
            if length >= self.min_crack_length_px and aspect >= self.aspect_ratio_thresh:
                defect_type = 'crack'
            else:
                # if area large and solidity high -> hole/spot
                if area > 500 and solidity > 0.6:
                    defect_type = 'hole_or_spot'
                else:
                    defect_type = 'possible_defect'
            defects.append({
                'type': defect_type,
                'bbox': (int(x0), int(y0), int(w0), int(h0)),
                'contour': cnt,
                'area': float(area),
                'length': int(length),
                'solidity': float(solidity),
                'confidence': 0.6  # heuristic confidence
            })
        return defects, cleaned, ske

    # ---------- YOLO PIPELINE ----------
    def yolo_preprocess(self, image, target=640):
        # letterbox resize (maintain aspect)
        h0, w0 = image.shape[:2]
        r = min(target / w0, target / h0)
        nw, nh = int(w0 * r), int(h0 * r)
        resized = cv2.resize(image, (nw, nh), interpolation=cv2.INTER_LINEAR)
        canvas = np.full((target, target, 3), 114, dtype=np.uint8)
        dx = (target - nw) // 2
        dy = (target - nh) // 2
        canvas[dy:dy+nh, dx:dx+nw, :] = resized
        blob = cv2.dnn.blobFromImage(canvas, 1/255.0, (target, target), swapRB=True, crop=False)
        return blob, r, dx, dy, (w0, h0)

    def yolo_detect(self, image):
        """Return list of detections: {label, confidence, bbox=(x,y,w,h)} in original image coords"""
        if not self.yolo_loaded():
            return []
        blob, r, dx, dy, orig = self.yolo_preprocess(image, target=self.yolo_input_size)
        self.net.setInput(blob)
        outs = self.net.forward(self.output_layer_names)
        W_orig, H_orig = orig
        boxes = []
        confidences = []
        class_ids = []
        for out in outs:
            for det in out:
                scores = det[5:]
                class_id = int(np.argmax(scores))
                conf = float(scores[class_id]) * float(det[4])
                if conf < self.yolo_conf:
                    continue
                cx = det[0] * self.yolo_input_size
                cy = det[1] * self.yolo_input_size
                bw = det[2] * self.yolo_input_size
                bh = det[3] * self.yolo_input_size
                x = cx - bw/2
                y = cy - bh/2
                # map from letterbox to original
                x = (x - dx) / r
                y = (y - dy) / r
                bw = bw / r
                bh = bh / r
                # clamp and append
                x1 = max(0, min(W_orig-1, int(x)))
                y1 = max(0, min(H_orig-1, int(y)))
                w_box = int(max(1, min(W_orig - x1, bw)))
                h_box = int(max(1, min(H_orig - y1, bh)))
                boxes.append([x1, y1, w_box, h_box])
                confidences.append(float(conf))
                class_ids.append(class_id)
        # NMS
        idxs = cv2.dnn.NMSBoxes(boxes, confidences, self.yolo_conf, self.yolo_nms)
        detections = []
        if len(idxs) > 0:
            for i in idxs.flatten():
                label = self.class_names[class_ids[i]] if class_ids[i] < len(self.class_names) else str(class_ids[i])
                detections.append({
                    'type': label,
                    'confidence': confidences[i],
                    'bbox': tuple(boxes[i])
                })
        return detections

    def is_yolo_loaded(self):
        return getattr(self, 'net', None) is not None and hasattr(self, 'output_layer_names')

    # ---------- MULTI-METHOD MERGE ----------
    def merge_detections(self, classical_list, yolo_list):
        """
        Merge both lists: if YOLO detects a defect overlapping classical, prefer YOLO label & confidence.
        Use IoU threshold to merge (0.3).
        """
        merged = []
        used = set()
        def iou(boxA, boxB):
            xA1,yA1,wA,hA = boxA
            xA2,yA2 = xA1+wA, yA1+hA
            xB1,yB1,wB,hB = boxB
            xB2,yB2 = xB1+wB, yB1+hB
            interX1 = max(xA1,xB1)
            interY1 = max(yA1,yB1)
            interX2 = min(xA2,xB2)
            interY2 = min(yA2,yB2)
            interW = max(0, interX2 - interX1)
            interH = max(0, interY2 - interY1)
            inter = interW * interH
            areaA = wA * hA
            areaB = wB * hB
            union = areaA + areaB - inter
            return inter/union if union>0 else 0

        # match YOLO to classical
        YOLO_USED = [False]*len(yolo_list)
        for c in classical_list:
            best_i = -1
            best_iou = 0
            for j, y in enumerate(yolo_list):
                if YOLO_USED[j]:
                    continue
                cur_iou = iou(c['bbox'], y['bbox'])
                if cur_iou > best_iou:
                    best_i = j
                    best_iou = cur_iou
            if best_i != -1 and best_iou > 0.3:
                # merge - take YOLO label
                merged.append({
                    'type': yolo_list[best_i]['type'],
                    'bbox': yolo_list[best_i]['bbox'],
                    'confidence': max(c.get('confidence',0.5), yolo_list[best_i].get('confidence',0.5))
                })
                YOLO_USED[best_i] = True
            else:
                # keep classical result (lower-confidence)
                merged.append({
                    'type': c.get('type','possible_defect'),
                    'bbox': c.get('bbox'),
                    'confidence': c.get('confidence', 0.5)
                })
        # add unmatched YOLO
        for j, y in enumerate(yolo_list):
            if not YOLO_USED[j]:
                merged.append({'type': y['type'], 'bbox': y['bbox'], 'confidence': y['confidence']})
        return merged

    # ---------- TOP LEVEL ----------
    def detect(self, image_path):
        img = cv2.imread(image_path)
        if img is None:
            raise ValueError("Could not read image")
        classical, cleaned_mask, skeleton = self.classical_detect(img)
        yolo_res = []
        if self.is_yolo_loaded():
            try:
                yolo_res = self.yolo_detect(img)
            except Exception as e:
                print("[WARN] YOLO failed at detect:", e)
                yolo_res = []
        merged = self.merge_detections(classical, yolo_res)
        # annotate image copy
        annotated = img.copy()
        type_count = {}
        for d in merged:
            x,y,w,h = d['bbox']
            lbl = d['type']
            type_count[lbl] = type_count.get(lbl, 0) + 1
            color = (0,255,0) if (lbl not in ['possible_defect','hole_or_spot']) else (0,0,255)
            cv2.rectangle(annotated, (x,y), (x+w, y+h), color, 2)
            cv2.putText(annotated, f"{lbl}:{d['confidence']:.2f}", (x, max(15,y-6)), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 1)
        # top-left summary
        cv2.putText(annotated, f"Defects: {len(merged)}", (10,30), cv2.FONT_HERSHEY_SIMPLEX, 1.0, (255,255,255), 2)
        return {
            'annotated_image': annotated,
            'defects': merged,
            'counts': type_count
        }
