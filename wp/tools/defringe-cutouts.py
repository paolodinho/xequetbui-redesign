#!/usr/bin/env python3
"""Làm sạch viền sáng quanh ảnh máy đã tách nền: co mép 1px, làm mềm, tô lại màu viền bằng màu phần trong.
Chạy sau make-cutouts.py (chỉ chạy 1 lần cho mỗi ảnh mới)."""
import glob, os, cv2, numpy as np
D = os.path.expanduser('~/Local Sites/xequetbui/app/public/wp-content/uploads/icd-cutout')
for f in sorted(glob.glob(D + '/*.png')):
    im = cv2.imread(f, cv2.IMREAD_UNCHANGED)
    if im is None or im.shape[2] != 4: continue
    a = im[..., 3]
    core = (a > 110).astype(np.uint8) * 255
    soft = cv2.GaussianBlur(core, (0, 0), 0.7)
    inner = cv2.erode(core, np.ones((3, 3), np.uint8))          # vùng chắc chắn là máy
    band = (inner == 0).astype(np.uint8) * 255                    # dải viền cần tô lại màu
    rgb = cv2.inpaint(np.ascontiguousarray(im[..., :3]), band, 2, cv2.INPAINT_TELEA)
    out = np.dstack([rgb, soft])
    ys, xs = np.where(soft > 40)
    out = out[ys.min():ys.max() + 1, xs.min():xs.max() + 1]
    cv2.imwrite(f, out); print(os.path.basename(f), out.shape[1], out.shape[0])
