(function () {
  var mn = document.getElementById('mnav'), mt = document.querySelector('.mtog');
  function mset(o) { if (!mn) return; mn.hidden = !o; mt.setAttribute('aria-expanded', o); document.documentElement.classList.toggle('mnav-open', o); }
  if (mt) mt.addEventListener('click', function () { mset(mn.hidden); });
  document.querySelectorAll('[data-mclose]').forEach(function (b) { b.addEventListener('click', function () { mset(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') mset(false); });
  document.addEventListener('click', function (e) {
    var a = e.target.closest('[data-sp]'); if (!a) return;
    var i = document.getElementById('sp'); if (i) i.value = a.getAttribute('data-sp');
  });
  document.querySelectorAll('[data-clamp]').forEach(function (el) {
    if (el.scrollHeight < 620) return;
    el.classList.add('is-clamped');
    var b = document.createElement('button'); b.type = 'button'; b.className = 'clamp-btn'; b.textContent = 'Xem đầy đủ nội dung';
    b.addEventListener('click', function () { var c = el.classList.toggle('is-clamped'); b.textContent = c ? 'Xem đầy đủ nội dung' : 'Thu gọn'; });
    el.parentNode.insertBefore(b, el.nextSibling);
  });
  var els = document.querySelectorAll('.box, .quote__in, .ft__s');
  if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var io = new IntersectionObserver(function (es) {
    es.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } });
  }, { rootMargin: '0px 0px -8% 0px' });
  els.forEach(function (el, i) { if (el.getBoundingClientRect().top > innerHeight) { el.classList.add('reveal'); io.observe(el); } });
})();

/* Mobile: menu danh mục ở trang danh mục thu gọn sẵn, bấm tiêu đề "Danh mục" để mở - nội dung hiện ngay màn đầu */
(function () {
  var mq = window.matchMedia('(max-width:1024px)');
  document.querySelectorAll('.lay__s').forEach(function (s) {
    var h = s.querySelector('h4'), c = s.querySelector('.cats'); if (!h || !c) return;
    h.setAttribute('role', 'button'); h.setAttribute('tabindex', '0'); h.setAttribute('aria-expanded', 'false'); h.classList.add('lay__tog');
    function set(o) { s.classList.toggle('is-open', o); h.setAttribute('aria-expanded', o); }
    h.addEventListener('click', function () { if (mq.matches) set(!s.classList.contains('is-open')); });
    h.addEventListener('keydown', function (e) { if ((e.key === 'Enter' || e.key === ' ') && mq.matches) { e.preventDefault(); set(!s.classList.contains('is-open')); } });
  });
})();

/* Giỏ báo giá (lưu trên trình duyệt), nút lên đầu trang, mục lục tự động */
(function () {
  var KEY = 'icd_quote_cart', cart = [];
  try { cart = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { cart = []; }
  var panel = document.getElementById('qcart'), list = document.querySelector('[data-cart-list]'), empty = document.querySelector('[data-cart-empty]');
  function save() { try { localStorage.setItem(KEY, JSON.stringify(cart)); } catch (e) {} }
  function render() {
    document.querySelectorAll('[data-cart-n]').forEach(function (n) { n.textContent = cart.length; n.hidden = !cart.length; });
    if (!list) return; list.innerHTML = '';
    cart.forEach(function (it, i) {
      var li = document.createElement('li'), s = document.createElement('span'), b = document.createElement('button');
      s.textContent = it.t; b.type = 'button'; b.setAttribute('aria-label', 'Bỏ sản phẩm'); b.innerHTML = '&times;';
      b.addEventListener('click', function () { cart.splice(i, 1); save(); render(); });
      li.appendChild(s); li.appendChild(b); list.appendChild(li);
    });
    if (empty) empty.hidden = !!cart.length;
    var go = document.querySelector('[data-cart-send]'); if (go) go.classList.toggle('is-off', !cart.length);
  }
  function open(o) { if (!panel) return; panel.hidden = !o; document.documentElement.classList.toggle('mnav-open', o); }
  document.addEventListener('click', function (e) {
    var a = e.target.closest('[data-addq]');
    if (a) { var id = a.getAttribute('data-addq'); if (!cart.some(function (c) { return c.id === id; })) cart.push({ id: id, t: a.getAttribute('data-t') }); save(); render(); a.classList.add('is-added'); a.innerHTML = '&#10003; Đã thêm vào giỏ'; setTimeout(function () { open(true); }, 150); return; }
    if (e.target.closest('[data-cart-open]')) { open(true); return; }
    if (e.target.closest('[data-cart-close]')) { open(false); return; }
    var g = e.target.closest('[data-cart-send]');
    if (g) { open(false); var i = document.getElementById('sp'); if (i && cart.length) i.value = cart.map(function (c) { return c.t; }).join('; '); }
  });
  render();
  var tt = document.querySelector('.totop');
  if (tt) { tt.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    var tick = function () { tt.hidden = window.scrollY < 500; }; window.addEventListener('scroll', tick, { passive: true }); tick(); }
  /* Mục lục: chuyển mục lục cũ (nếu có) lên đầu phần mô tả; chưa có thì tự dựng từ H2/H3 */
  document.querySelectorAll('.prose--pd, .art').forEach(function (pr) {
    var slot = pr.querySelector('[data-toc-after]'); var old = pr.querySelector('#ez-toc-container, .ez-toc-container');
    if (old) { old.classList.add('toc'); if (slot) slot.replaceWith(old); else pr.insertBefore(old, pr.children[1] || null); return; }
    var hs = pr.querySelectorAll('h2, h3'); var items = [];
    hs.forEach(function (h, i) { var t = h.textContent.trim(); if (!t || t === 'Thông tin chi tiết' || t === 'Video giới thiệu') return; if (!h.id) h.id = 'm' + i; items.push([h.id, t, h.tagName]); });
    if (items.length < 3) return;
    var box = document.createElement('nav'); box.className = 'toc'; box.setAttribute('aria-label', 'Mục lục'); box.innerHTML = '<b>Mục lục</b><ol></ol>';
    items.forEach(function (x) { var li = document.createElement('li'), a = document.createElement('a'); a.href = '#' + x[0]; a.textContent = x[1]; if (x[2] === 'H3') li.className = 'sub'; li.appendChild(a); box.querySelector('ol').appendChild(li); });
    if (slot) slot.replaceWith(box); else pr.insertBefore(box, pr.children[1] || null);
  });
})();
