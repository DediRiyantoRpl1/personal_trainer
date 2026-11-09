/* Panel admin: tema, menu HP, lompat cepat (Ctrl+K), filter daftar, tab, toast. */
(() => {
  'use strict';
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const root = document.documentElement;

  /* ── halaman dipulihkan dari cache (tombol Back): muat ulang agar sesi dicek server ── */
  addEventListener('pageshow', (e) => { if (e.persisted) location.reload(); });

  /* ── tema terang/gelap ── */
  const setTheme = (t) => {
    root.dataset.theme = t;
    try { localStorage.setItem('adm-theme', t); } catch (e) {}
    $$('[data-theme-toggle]').forEach((b) => b.setAttribute('aria-pressed', String(t === 'dark')));
  };
  $$('[data-theme-toggle]').forEach((b) => {
    b.setAttribute('aria-pressed', String(root.dataset.theme === 'dark'));
    b.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
  });

  /* ── menu samping di HP ── */
  const side = $('#side'), scrim = $('#scrim');
  const drawer = (open) => {
    if (!side) return;
    side.classList.toggle('open', open);
    scrim.classList.toggle('on', open);
    document.body.classList.toggle('lock', open);
  };
  $$('[data-drawer]').forEach((b) => b.addEventListener('click', () => drawer(!side.classList.contains('open'))));
  if (scrim) scrim.addEventListener('click', () => drawer(false));

  /* ── toast ── */
  const host = $('#toasts');
  const toast = (msg) => {
    if (!host || !msg) return;
    const el = document.createElement('div');
    el.className = 'toast';
    el.setAttribute('role', 'status');
    const dot = document.createElement('i');
    const txt = document.createElement('span');
    txt.textContent = msg;
    el.append(dot, txt);
    host.appendChild(el);
    requestAnimationFrame(() => el.classList.add('in'));
    setTimeout(() => { el.classList.remove('in'); setTimeout(() => el.remove(), 400); }, 4200);
  };
  if (window.__flash) toast(window.__flash);

  /* ── tab (papan jadwal hari ini / besok) ── */
  $$('[data-tabroot]').forEach((rootEl) => {
    const tabs = $$('[data-tab]', rootEl);
    tabs.forEach((t) => t.addEventListener('click', () => {
      tabs.forEach((x) => x.setAttribute('aria-selected', String(x === t)));
      $$('[data-pane]', rootEl).forEach((p) => { p.hidden = p.dataset.pane !== t.dataset.tab; });
    }));
  });

  /* ── filter & pencarian daftar ── */
  $$('[data-filterable]').forEach((scope) => {
    const input = $('[data-search]', scope);
    const chips = $$('[data-chip]', scope);
    const items = $$('[data-item]', scope);
    let status = 'all';

    const apply = () => {
      const q = (input ? input.value : '').trim().toLowerCase();
      items.forEach((it) => {
        const okStatus = status === 'all' || it.dataset.status === status;
        const okText = !q || (it.dataset.text || '').includes(q);
        it.hidden = !(okStatus && okText);
      });
      $$('[data-section]', scope).forEach((sec) => {
        const all = $$('[data-item]', sec);
        const vis = all.filter((i) => !i.hidden).length;
        const c = $('[data-count]', sec);
        if (c) c.textContent = vis;
        const none = $('[data-none]', sec);
        if (none) none.hidden = !(all.length > 0 && vis === 0);
      });
    };

    chips.forEach((c) => c.addEventListener('click', () => {
      status = c.dataset.chip;
      chips.forEach((x) => x.setAttribute('aria-pressed', String(x === c)));
      apply();
    }));
    if (input) input.addEventListener('input', apply);
  });

  /* ── lompat cepat (Ctrl/Cmd+K) ── */
  const pal = $('#pal');
  if (pal) {
    const input = $('input', pal), list = $('ul', pal);
    const entries = window.__nav || [];
    let idx = 0, shown = [];

    const go = (it) => {
      if (!it) return;
      if (it.action === 'logout') { const f = $('#logout-form'); if (f) f.submit(); return; }
      if (it.external) { window.open(it.href, '_blank', 'noopener'); close(); return; }
      location.href = it.href;
    };
    const render = () => {
      const q = input.value.trim().toLowerCase();
      shown = entries.filter((e) => !q || (e.label + ' ' + (e.kw || '')).toLowerCase().includes(q));
      idx = Math.min(idx, Math.max(0, shown.length - 1));
      list.replaceChildren();
      if (!shown.length) {
        const li = document.createElement('li');
        li.className = 'none';
        li.textContent = 'Tidak ada yang cocok. Coba kata lain, misalnya "booking" atau "pesan".';
        list.append(li);
        return;
      }
      shown.forEach((e, n) => {
        const li = document.createElement('li');
        li.setAttribute('role', 'option');
        li.setAttribute('aria-selected', String(n === idx));
        const el = document.createElement(e.action ? 'button' : 'a');
        if (!e.action) el.href = e.href;
        if (e.action) el.type = 'button';
        el.textContent = e.label;
        if (e.hint) { const s = document.createElement('small'); s.textContent = e.hint; el.append(s); }
        el.addEventListener('click', (ev) => { if (e.action || e.external) { ev.preventDefault(); go(e); } });
        el.addEventListener('mousemove', () => {
          if (idx === n) return;
          idx = n;
          $$('li', list).forEach((x, k) => x.setAttribute('aria-selected', String(k === n)));
        });
        li.append(el);
        list.append(li);
      });
    };
    const open = () => { pal.classList.add('open'); input.value = ''; idx = 0; render(); input.focus(); };
    const close = () => pal.classList.remove('open');

    $$('[data-palette]').forEach((b) => b.addEventListener('click', open));
    pal.addEventListener('mousedown', (e) => { if (e.target === pal) close(); });
    input.addEventListener('input', () => { idx = 0; render(); });
    input.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(shown.length - 1, idx + 1); render(); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(0, idx - 1); render(); }
      else if (e.key === 'Enter') { e.preventDefault(); go(shown[idx]); }
    });

    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        pal.classList.contains('open') ? close() : open();
      } else if (e.key === 'Escape') {
        if (pal.classList.contains('open')) close(); else drawer(false);
      } else if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName)) {
        const s = $('[data-search]');
        if (s) { e.preventDefault(); s.focus(); }
      }
    });
  }
})();
