#!/usr/bin/env python3
"""Đồng bộ bản xem giao diện tĩnh từ http://xequetbui.local lên GitHub Pages (nhánh gh-pages).
Dùng: python3 sync-demo.py [thư-mục-làm-việc]   (mặc định ~/Claude-Workspace/xequetbui/demo-static)
Ảnh trong wp-content/uploads được giữ lại giữa các lần chạy; chỉ html/css/js được làm mới."""
import os, re, shutil, subprocess, sys, urllib.parse, urllib.request
W = os.path.expanduser(sys.argv[1] if len(sys.argv) > 1 else '~/Claude-Workspace/xequetbui/demo-static')
SRC = os.path.expanduser('~/Local Sites/xequetbui/app/public/')
REPO = 'https://github.com/paolodinho/xequetbui-redesign.git'; PFX = '/xequetbui-redesign/'
NEW = W + '/_new'; OUT = W + '/site'
os.makedirs(W, exist_ok=True); shutil.rmtree(NEW, ignore_errors=True)
subprocess.run(['wget', '--mirror', '--convert-links', '--adjust-extension', '--page-requisites', '--no-parent', '-e', 'robots=off', '--no-host-directories',
    '--timeout=30', '--tries=2', '--reject-regex', r'(\?(s|p|page_id|selected|subtotal|total|add-to-cart|replytocom|post_type)=|/wp-admin|/wp-json|/cart/|/checkout/|/my-account/|/feed|xmlrpc|/comments)',
    '-P', NEW, '-o', W + '/wget.log', 'http://xequetbui.local/'])
# gộp: giữ ảnh cũ, ghi đè phần còn lại
if os.path.exists(OUT + '/.git'): shutil.move(OUT + '/.git', W + '/_git')
shutil.rmtree(OUT, ignore_errors=True)
os.makedirs(OUT); 
if os.path.exists(W + '/uploads-keep'): shutil.copytree(W + '/uploads-keep', OUT + '/wp-content/uploads')
shutil.copytree(NEW, OUT, dirs_exist_ok=True)
if os.path.exists(W + '/_git'): shutil.move(W + '/_git', OUT + '/.git')
os.chdir(OUT)
for r, ds, fs in os.walk('.'):
    for f in fs:
        if '?' in f: os.replace(os.path.join(r, f), os.path.join(r, re.sub(r'\?.*$', '', f)))
ver = re.compile(r'(?:%3F|\?)(?:ver|v)=[^"\'\s>)]*')
def rd(p): return open(p, encoding='utf-8', errors='surrogateescape').read()
def wr(p, s): open(p, 'w', encoding='utf-8', errors='surrogateescape').write(s)
need = set()
for r, ds, fs in os.walk('.'):
    if r.startswith(('./wp-content/plugins', './wp-includes', './.git')): continue
    for f in fs:
        if not f.endswith(('.html', '.css')): continue
        p = os.path.join(r, f); s = rd(p); t = ver.sub('', s)
        if f.endswith('.html'):
            pre = '../' * r.count('/')
            t = t.replace('http://xequetbui.local/', pre).replace('https://xequetbui.local/', pre)
            t = re.sub(r"(--[a-z]+:url\(['\"]?)(?:\.\./)*(wp-content/)", r"\1" + PFX + r"\2", t)
            t = re.sub(r'(wp-content/themes/icd-cleaning/assets/(?:css|js)/[\w.-]+\.(?:css|js))(?![\w?])', r'\1?v=' + str(int(__import__('time').time())), t)  # chống cache trình duyệt
            if 'noindex' not in t: t = t.replace('<head>', '<head>\n<meta name="robots" content="noindex, nofollow">', 1)
        if t != s: wr(p, t)
        for m in re.finditer(r'wp-content/uploads/[^"\'\s,)<>]+\.(?:jpe?g|png|webp|gif|svg)', t): need.add(urllib.parse.unquote(m.group(0)))
miss = 0
for rel in need:
    if os.path.exists(rel): continue
    for cand in (rel, re.sub(r'-\d+x\d+(?=\.\w+$)', '', rel)):
        if os.path.exists(SRC + cand):
            os.makedirs(os.path.dirname(rel), exist_ok=True); shutil.copy(SRC + cand, rel); break
    else: miss += 1
# phân trang: /x.html/page/N không tạo được thành tệp (x.html đã là tệp) -> đổi thành x-page-N.html và tải trang đó về
pg = re.compile(r'((?:\.\./)*)([\w-]+)\.html/page/(\d+)/?')
def html_files():
    for r, ds, fs in os.walk('.'):
        if r.startswith(('./wp-content', './wp-includes', './.git')): continue
        for f in fs:
            if f.endswith('.html'): yield os.path.join(r, f)
done = set()
while True:
    todo = set()
    for p in html_files():
        for m in pg.finditer(rd(p)):
            if (m.group(2), m.group(3)) not in done: todo.add((m.group(2), m.group(3)))
    if not todo: break
    for slug, n in todo:
        done.add((slug, n))
        try: h = urllib.request.urlopen('http://xequetbui.local/%s.html/page/%s/' % (slug, n), timeout=90).read().decode('utf-8', 'surrogateescape')
        except Exception: continue
        h = ver.sub('', h).replace('http://xequetbui.local/', '').replace('https://xequetbui.local/', '')
        h = re.sub(r"(wp-content/themes/icd-cleaning/assets/(?:css|js)/[\w.-]+\.(?:css|js))(?![\w?])", r"\1?v=" + str(int(__import__('time').time())), h)
        if 'noindex' not in h: h = h.replace('<head>', '<head>\n<meta name="robots" content="noindex, nofollow">', 1)
        wr('%s-page-%s.html' % (slug, n), h)
        for m in re.finditer(r'wp-content/uploads/[^"\'\s,)<>]+\.(?:jpe?g|png|webp|gif|svg)', h): need.add(urllib.parse.unquote(m.group(0)))
for p in list(html_files()):
    t = rd(p); u = pg.sub(r'\1\2-page-\3.html', t)
    if u != t: wr(p, u)
for rel in need:
    if os.path.exists(rel): continue
    for cand in (rel, re.sub(r'-\d+x\d+(?=\.\w+$)', '', rel)):
        if os.path.exists(SRC + cand):
            os.makedirs(os.path.dirname(rel), exist_ok=True); shutil.copy(SRC + cand, rel); break
open('robots.txt', 'w').write('User-agent: *\nDisallow: /\n'); open('.nojekyll', 'w').write('')
if not os.path.exists('.git'):
    subprocess.run(['git', 'init', '-q', '-b', 'gh-pages']); subprocess.run(['git', 'remote', 'add', 'origin', REPO])
subprocess.run(['git', 'add', '-A']); subprocess.run(['git', 'commit', '-qm', 'Cập nhật bản xem giao diện\n\nCo-Authored-By: Claude Sonnet 5.5 <noreply@anthropic.com>'])
subprocess.run(['git', 'push', '-q', '-f', 'origin', 'gh-pages'])
print('xong, ảnh thiếu (bỏ qua):', miss)
