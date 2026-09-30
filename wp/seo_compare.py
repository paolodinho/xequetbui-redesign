#!/usr/bin/env python3
"""So sánh title/meta description/canonical/H1/robots/JSON-LD của site cũ (crawl) với WP Local."""
import openpyxl,urllib.request,re,html,concurrent.futures as cf,sys,unicodedata,urllib.parse
R="/Volumes/Extreme SSD/Projects/ICD do thi/06-thiet-ke-xequetbui"
rows=[r for r in openpyxl.load_workbook(f"{R}/noi-dung/URL-inventory-xequetbui-2026-09-29.xlsx")["URL_inventory"].iter_rows(min_row=2,values_only=True) if r[19]!="EV chắc chắn"]
def meta(h,n,a="name"):
    m=re.search(r'<meta[^>]+%s=["\']%s["\'][^>]*content=["\']([^"\']*)'%(a,re.escape(n)),h,re.I) or re.search(r'<meta[^>]+content=["\']([^"\']*)["\'][^>]+%s=["\']%s["\']'%(a,re.escape(n)),h,re.I)
    return html.unescape(m.group(1)).strip() if m else ""
def get(r):
    u="http://xequetbui.local"+r[3]
    try: h=urllib.request.urlopen(urllib.request.Request(u,headers={"User-Agent":"x"}),timeout=60).read().decode("utf8","ignore")
    except Exception as e: return (r,None)
    t=re.search(r"<title>(.*?)</title>",h,re.S|re.I)
    can=re.search(r'<link[^>]+rel=["\']canonical["\'][^>]*href=["\']([^"\']+)',h,re.I)
    return (r,dict(title=html.unescape(re.sub(r"\s+"," ",t.group(1))).strip() if t else "",desc=meta(h,"description"),robots=meta(h,"robots"),
      can=urllib.parse.urlparse(can.group(1)).path if can else "",h1=len(re.findall(r"<h1[ >]",h,re.I)),ld=len(re.findall("application/ld\\+json",h))))
with cf.ThreadPoolExecutor(4) as ex: res=list(ex.map(get,rows))
n=lambda s:unicodedata.normalize("NFC",html.unescape(s or "")).replace("–","-").replace("&#8211;","-").strip()
st={"title":0,"desc":0,"can":0,"h1":0,"ld_old_gt_new":0,"robots":0};bad=[]
for r,x in res:
    if not x: bad.append((r[3],"LỖI")); continue
    d=[]
    if n(x["title"])!=n(r[9]): st["title"]+=1;d.append(("title",r[9],x["title"]))
    if n(x["desc"])!=n(r[10]): st["desc"]+=1;d.append(("desc",(r[10] or "")[:60],x["desc"][:60]))
    oc=urllib.parse.urlparse(r[11] or "").path.rstrip("/")
    if r[11] and oc!=x["can"].rstrip("/"): st["can"]+=1;d.append(("canonical",oc,x["can"]))
    if (r[15] or 0)!=x["h1"]: st["h1"]+=1;d.append(("h1",r[15],x["h1"]))
    if x["ld"]==0: st["ld_old_gt_new"]+=1;d.append(("ld",r[16],x["ld"]))
    orb=("noindex" in (r[13] or "")); nrb=("noindex" in x["robots"])
    if orb!=nrb: st["robots"]+=1;d.append(("robots",r[13],x["robots"]))
    if d: bad.append((r[0],r[3],d))
print(len(res),st)
import json;json.dump(bad,open(f"{R}/tham-khao/data/seo_diff.json","w"),ensure_ascii=False,indent=1)
for b in bad[:12]: print(b)
