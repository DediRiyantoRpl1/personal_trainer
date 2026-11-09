/* JS ringan untuk semua halaman selain beranda (beranda memakai app.js) */
(() => {
  'use strict';
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  /* toast */
  const toastHost = $('#toasts');
  function toast(msg) {
    if (!toastHost) return;
    const el = document.createElement('div');
    el.className = 'toast';
    const dot = document.createElement('i');
    const txt = document.createElement('span');
    txt.textContent = msg;
    el.append(dot, txt);
    toastHost.appendChild(el);
    requestAnimationFrame(() => el.classList.add('in'));
    setTimeout(() => { el.classList.remove('in'); setTimeout(() => el.remove(), 400); }, 3600);
  }

  /* menu mobile */
  const toggle = $('.nav-toggle'), nav = $('.nav');
  if (toggle && nav) {
    const close = () => { nav.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); };
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });
    $$('a', nav).forEach(a => a.addEventListener('click', close));
    document.addEventListener('click', e => {
      if (nav.classList.contains('open') && !nav.contains(e.target) && !toggle.contains(e.target)) close();
    });
  }

  /* progress bar */
  const bar = $('#progressBar');
  if (bar) {
    const upd = () => {
      const d = document.documentElement, max = d.scrollHeight - d.clientHeight;
      bar.style.width = (max > 0 ? (d.scrollTop / max) * 100 : 0).toFixed(2) + '%';
    };
    addEventListener('scroll', upd, { passive: true });
    upd();
  }

  /* reveal saat scroll */
  const items = $$('.reveal');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(es => es.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('in');
      io.unobserve(e.target);
    }), { threshold: .12 });
    items.forEach(el => io.observe(el));
  } else {
    items.forEach(el => el.classList.add('in'));
  }

  /* pesan flash dari server (mis. setelah kirim pesan kontak) */
  const flash = $('#flash');
  if (flash && flash.dataset.msg) toast(flash.dataset.msg);
})();
