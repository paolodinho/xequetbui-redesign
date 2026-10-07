#!/usr/bin/env python3
"""Chỉnh tay 2 ảnh mà rembg làm mất chi tiết (mâm máy đánh sàn, bóng sáng): dùng bản tách của PHP rồi xoá lỗ tay cầm/bóng đổ."""
import os, numpy as np
from PIL import Image, ImageFilter
D = os.path.expanduser('~/Local Sites/xequetbui/app/public/wp-content/uploads/icd-cutout')
for f in ['17597.png', '17292.png']:
    im = Image.open(f'{D}/{f}').convert('RGBA'); arr = np.array(im).astype(int)
    rgb = arr[..., :3]; mx = rgb.max(2); mn = rgb.min(2); lum = rgb.mean(2); a = arr[..., 3].copy(); h, w = a.shape
    light = (lum > 200) & ((mx - mn) < 25)
    if f == '17597.png':
        top = np.zeros_like(light); top[:int(h * 0.20), :] = True; a[light & top] = 0
        dark = lum < 95
        for x in range(w):
            ys = np.where(dark[:, x] & (a[:, x] > 0))[0]
            if len(ys): a[ys.max() + 3:, x] = 0
    else:
        a[(lum > 222) & ((mx - mn) < 22)] = 0
    al = Image.fromarray(a.astype('uint8')).filter(ImageFilter.MinFilter(3)).filter(ImageFilter.GaussianBlur(0.6))
    arr[..., 3] = np.array(al); out = Image.fromarray(arr.astype('uint8'))
    out = out.crop(out.getchannel('A').point(lambda v: 255 if v > 40 else 0).getbbox()); out.save(f'{D}/{f}')
