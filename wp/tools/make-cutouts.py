#!/usr/bin/env python3
"""Tách nền ảnh sản phẩm bằng rembg (u2net) cho banner/hero. Dùng: python3 make-cutouts.py tids.json
tids.json = {"<attachment_id>": "<đường dẫn ảnh gốc>"}. Kết quả: wp-content/uploads/icd-cutout/<id>.png (PHP icd_cutout dùng file này nếu có)."""
import json, os, sys
from PIL import Image
from rembg import remove, new_session
OUT = os.path.expanduser('~/Local Sites/xequetbui/app/public/wp-content/uploads/icd-cutout')
os.makedirs(OUT, exist_ok=True)
sess = new_session('u2net')
for tid, src in json.load(open(sys.argv[1])).items():
    im = Image.open(src).convert('RGB'); im.thumbnail((900, 900))
    o = remove(im, session=sess, alpha_matting=True, alpha_matting_foreground_threshold=240, alpha_matting_background_threshold=15, alpha_matting_erode_size=8)
    bb = o.getchannel('A').point(lambda v: 255 if v > 40 else 0).getbbox()
    if bb: o = o.crop(bb)
    o.save(f'{OUT}/{tid}.png'); print(tid, o.size)
