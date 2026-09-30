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
