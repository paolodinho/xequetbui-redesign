#!/usr/bin/env python3
"""Tách nền ảnh sản phẩm bằng rembg (u2net) cho banner/hero. Dùng: python3 make-cutouts.py tids.json
tids.json = {"<attachment_id>": "<đường dẫn ảnh gốc>"}. Kết quả: wp-content/uploads/icd-cutout/<id>.png (PHP icd_cutout dùng file này nếu có)."""
import json, os, sys
from PIL import Image
from rembg import remove, new_session
OUT = os.path.expanduser('~/Local Sites/xequetbui/app/public/wp-content/uploads/icd-cutout')
os.makedirs(OUT, exist_ok=True)
sess = new_session('isnet-general-use')  # sạch hơn u2net ở vùng trống giữa khung/tay cầm
# ảnh xe chở rác 17511: isnet làm rỗng thùng xe xanh, giữ bản u2net (dùng U2=1 để tách bằng u2net)
for tid, src in json.load(open(sys.argv[1])).items():
    if tid == '17511': sess = new_session('u2net')
    else: sess = new_session('isnet-general-use')
    im = Image.open(src).convert('RGB'); im.thumbnail((900, 900))
    o = remove(im, session=sess)
    # bỏ mảng rời nhỏ (logo hãng...): giữ mảng lớn nhất và mảng >= 16% mảng lớn nhất
    import numpy as np, cv2
    arr = np.array(o); n, lab, st, _ = cv2.connectedComponentsWithStats((arr[..., 3] > 100).astype(np.uint8), connectivity=4)
    if n > 2:
        mx = st[1:, cv2.CC_STAT_AREA].max()
        for i in range(1, n):
            if st[i, cv2.CC_STAT_AREA] < mx * 0.16: arr[lab == i, 3] = 0
        o = Image.fromarray(arr)
    bb = o.getchannel('A').point(lambda v: 255 if v > 40 else 0).getbbox()
    if bb: o = o.crop(bb)
    o.save(f'{OUT}/{tid}.png'); print(tid, o.size)
