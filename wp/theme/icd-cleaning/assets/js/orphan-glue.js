/* Chống chữ mồ côi (1 từ trên 1 dòng) - dán vào JS của mọi site.
   Nối bằng NBSP từ cuối cùng với từ liền trước trong mọi tiêu đề, đoạn, mục danh sách, nút, chú thích.
   Đi kèm CSS: h1..h6,.title{text-wrap:balance} p,li,figcaption{text-wrap:pretty} */
(function () {
  var SEL = 'h1,h2,h3,h4,h5,h6,p,li,figcaption,blockquote,dd,dt,.btn,.kicker,[data-nowrap-tail]';
  function glue(root) {
    (root || document).querySelectorAll(SEL).forEach(function (el) {
      if (el.dataset.orphanDone) return;
      var w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null), last = null, n;
      while ((n = w.nextNode())) if (n.nodeValue.trim()) last = n;
      if (!last) return;
      var t = last.nodeValue.replace(/\s+$/, ''), m = t.match(/^([\s\S]*\S)\s+(\S+)$/);
      if (m && /\S\s+\S/.test(t)) {
        var mm = m[1].match(/^([\s\S]*\s)?(\S+)$/);
        if (m[2].indexOf('-') > -1 || (mm && mm[2].indexOf('-') > -1)) { /* mã có gạch nối: bọc nowrap để không ngắt sau dấu gạch */
          var head = mm && mm[1] ? mm[1] : '', span = document.createElement('span');
          span.style.whiteSpace = 'nowrap'; span.textContent = (mm ? mm[2] : m[1]) + '\u00a0' + m[2];
          var tail = last.nodeValue.slice(t.length); last.nodeValue = head;
          last.parentNode.insertBefore(span, last.nextSibling);
          if (tail) last.parentNode.insertBefore(document.createTextNode(tail), span.nextSibling);
        } else last.nodeValue = m[1] + '\u00a0' + m[2] + last.nodeValue.slice(t.length);
      }
      else if (last.previousSibling === null && last.parentNode !== el) return; /* từ cuối nằm riêng trong thẻ con: bỏ qua */
      el.dataset.orphanDone = '1';
    });
  }
  if (document.readyState !== 'loading') glue(); else document.addEventListener('DOMContentLoaded', function () { glue(); });
  window.icdGlueOrphans = glue;
})();
