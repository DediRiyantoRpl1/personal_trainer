(() => {
  'use strict';

  /* ================= utils ================= */
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ================= toast ================= */
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
    setTimeout(() => { el.classList.remove('in'); setTimeout(() => el.remove(), 400); }, 3400);
  }

  /* ================= header / nav / progress ================= */
  const toggle   = $('.nav-toggle');
  const nav      = $('.nav');
  const links    = $$('a', nav);
  const progress = $('#progressBar');

  const closeNav = () => { nav.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); };
  toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });
  links.forEach(a => a.addEventListener('click', closeNav));
  document.addEventListener('click', e => {
    if (nav.classList.contains('open') && !nav.contains(e.target) && !toggle.contains(e.target)) closeNav();
  });

  function onScroll() {
    const doc = document.documentElement;
    const max = doc.scrollHeight - doc.clientHeight;
    progress.style.width = (max > 0 ? (doc.scrollTop / max) * 100 : 0).toFixed(2) + '%';
  }
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ================= reveal ================= */
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('in');
      io.unobserve(e.target);
    });
  }, { threshold: .12 });
  $$('.reveal').forEach(el => io.observe(el));

  /* ================= counters ================= */
  const cio = new IntersectionObserver(entries => entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el = e.target;
    const to = parseFloat(el.dataset.count);
    const dec = +(el.dataset.dec || 0);
    const t0 = performance.now();
    const step = t => {
      const p = reduceMotion ? 1 : Math.min((t - t0) / 1500, 1);
      el.textContent = (to * (1 - Math.pow(1 - p, 3))).toFixed(dec);
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
    cio.unobserve(el);
  }), { threshold: .4 });
  $$('[data-count]').forEach(el => cio.observe(el));

  /* ================= BMI calculator ================= */
  const cf = $('#calcForm'), out = $('#calcOut');
  if (cf) cf.addEventListener('submit', e => {
    e.preventDefault();
    const w = +cf.w.value, h = +cf.h.value, a = +cf.a.value, act = +cf.act.value;

    if (!(w >= 30 && w <= 250) || !(h >= 120 && h <= 230) || !(a >= 14 && a <= 90)) {
      toast('Isi berat, tinggi, dan usia dengan angka yang wajar.');
      return;
    }

    const bmi = w / Math.pow(h / 100, 2);
    const cat = bmi < 18.5 ? 'Kurus' : bmi < 25 ? 'Normal' : bmi < 30 ? 'Berlebih' : 'Obesitas';
    const bmr = 10 * w + 6.25 * h - 5 * a + (cf.s.value === 'm' ? 5 : -161);
    const tdee = Math.round(bmr * act);

    out.innerHTML = `
      <div><b>${bmi.toFixed(1)}</b><small>BMI · ${cat}</small></div>
      <div><b>${tdee.toLocaleString('id-ID')}</b><small>kkal / hari (maintenance)</small></div>
      <p>Untuk turun berat badan sehat, mulai dari sekitar <strong>${(tdee - 400).toLocaleString('id-ID')} kkal</strong>; untuk menambah otot sekitar <strong>${(tdee + 250).toLocaleString('id-ID')} kkal</strong>. Ini perkiraan umum, bukan saran medis.</p>`;
    out.classList.add('show');
    toast('Perhitungan selesai. Detail lengkap kita bahas di sesi gratis.');
  });

  /* ================= before / after ================= */
  const ba = $('#ba'), range = $('#baRange');
  if (ba && range) {
    range.addEventListener('input', () => ba.style.setProperty('--pos', range.value + '%'));
  }

  /* ================= testimonials ================= */
  const qs = $$('#slides blockquote'), dots = $('#dots');
  let i = 0, timer;
  if (qs.length && dots) qs.forEach((_, k) => {
    const b = document.createElement('button');
    b.setAttribute('aria-label', 'Testimoni ' + (k + 1));
    b.onclick = () => { go(k); auto(); };
    dots.appendChild(b);
  });
  const go = k => {
    i = k;
    qs.forEach((q, n) => q.classList.toggle('on', n === k));
    [...dots.children].forEach((d, n) => d.classList.toggle('on', n === k));
  };
  const auto = () => {
    clearInterval(timer);
    timer = setInterval(() => go((i + 1) % qs.length), 5500);
  };
  if (qs.length && dots) { go(0); auto(); }

  const bookForm = $('#bookForm');

  /* ================= booking form + slots (Laravel API) ================= */
  if (bookForm) {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const slotList = $('#slotList');
  const confirmEl = $('#confirm');
  const bookedWrap = $('#bookedWrap');
  const bookedList = $('#bookedList');
  const URL_ = bookForm.dataset;

  async function api(url, opts = {}) {
    const res = await fetch(url, {
      ...opts,
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
        ...(opts.body ? { 'Content-Type': 'application/json' } : {}),
        ...(opts.headers || {})
      },
      credentials: 'same-origin'
    });
    let data = {};
    try { data = await res.json(); } catch (e) {}
    if (!res.ok) {
      const first = data.errors ? Object.values(data.errors)[0][0] : null;
      const err = new Error(first || data.message || 'Terjadi kesalahan. Coba lagi.');
      err.status = res.status;
      err.fields = data.errors || {};
      throw err;
    }
    return data;
  }

  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const fmtDate = (d, opt) => new Date(d + 'T00:00').toLocaleDateString('id-ID', opt);

  const dateInput = bookForm.tgl;
  const today = new Date(); today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
  dateInput.min = today.toISOString().slice(0, 10);

  let curSlot = '';
  let slotReq = 0;

  async function renderSlots() {
    const d = dateInput.value;
    if (!d) { slotList.innerHTML = '<p class="slot-empty">Pilih tanggal dulu.</p>'; return; }
    const req = ++slotReq;
    slotList.innerHTML = '<p class="slot-empty">Memuat jam…</p>';
    try {
      const { slots } = await api(`${URL_.slotsUrl}?tanggal=${encodeURIComponent(d)}`);
      if (req !== slotReq) return; // tanggal sudah diganti lagi
      if (!slots.length) { slotList.innerHTML = '<p class="slot-empty">Tidak ada jam tersedia di tanggal ini.</p>'; return; }
      slotList.innerHTML = slots.map(s =>
        `<button type="button" class="slot-btn ${curSlot === s.jam ? 'on' : ''}" data-slot="${s.jam}" ${s.taken ? 'disabled' : ''}>${s.jam}${s.taken ? ' ✓' : ''}</button>`
      ).join('');
    } catch (e) {
      if (req === slotReq) slotList.innerHTML = '<p class="slot-empty">Gagal memuat jam. Coba pilih tanggal lagi.</p>';
    }
  }

  async function renderBooked() {
    if (URL_.auth !== '1') return;
    try {
      const { bookings } = await api(URL_.mineUrl);
      if (!bookings.length) { bookedWrap.hidden = true; bookedList.innerHTML = ''; return; }
      bookedWrap.hidden = false;
      bookedList.innerHTML = bookings.map(b => {
        const ds = fmtDate(b.tanggal, { weekday: 'short', day: 'numeric', month: 'short' });
        return `<li>
          <span class="bd-date">${ds} · ${esc(b.jam)}</span>
          <span class="bd-pkg">${esc(b.paket)}</span>
          <button class="bd-x" type="button" data-cancel="${b.id}" aria-label="Batalkan ${ds}">×</button>
        </li>`;
      }).join('');
    } catch (e) { /* diamkan: daftar booking opsional */ }
  }

  dateInput.addEventListener('change', () => { curSlot = ''; renderSlots(); });

  slotList.addEventListener('click', e => {
    const b = e.target.closest('[data-slot]');
    if (!b || b.disabled) return;
    curSlot = b.dataset.slot;
    $$('.slot-btn', slotList).forEach(x => x.classList.toggle('on', x === b));
  });

  bookedList.addEventListener('click', async e => {
    const b = e.target.closest('[data-cancel]');
    if (!b) return;
    b.disabled = true;
    try {
      await api(`${URL_.cancelUrl}/${b.dataset.cancel}`, { method: 'DELETE' });
      toast('Booking dibatalkan.');
    } catch (err) {
      toast(err.message);
    }
    renderBooked();
    renderSlots();
  });

  let sending = false;
  bookForm.addEventListener('submit', async e => {
    e.preventDefault();
    if (sending) return;
    confirmEl.classList.remove('show');

    if (URL_.auth !== '1') {
      toast('Masuk dulu untuk booking.');
      setTimeout(() => { location.href = URL_.loginUrl; }, 900);
      return;
    }

    const nama = bookForm.nama, wa = bookForm.wa, tgl = bookForm.tgl;
    const okN = nama.value.trim().length >= 2;
    const okW = /^(\+62|62|0)8\d{7,12}$/.test(wa.value.replace(/[\s-]/g, ''));
    const okD = !!tgl.value;
    const okS = !!curSlot;

    nama.classList.toggle('err', !okN);
    wa.classList.toggle('err', !okW);
    tgl.classList.toggle('err', !okD);

    if (!okN || !okW) { toast('Periksa nama dan WhatsApp dulu.'); return; }
    if (!okD) { toast('Pilih tanggal dulu.'); return; }
    if (!okS) { toast('Pilih jam dulu.'); return; }

    const payload = {
      paket: bookForm.paket.value,
      nama: nama.value.trim(),
      wa: wa.value.trim(),
      tanggal: tgl.value,
      jam: curSlot,
      target: bookForm.goal.value
    };

    const btn = bookForm.querySelector('button[type="submit"]');
    sending = true; btn.disabled = true;
    try {
      const { booking } = await api(URL_.storeUrl, { method: 'POST', body: JSON.stringify(payload) });

      const dateStr = fmtDate(booking.tanggal, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      confirmEl.classList.add('show');
      confirmEl.innerHTML = `✅ Terima kasih, <b>${esc(payload.nama.split(' ')[0])}</b>! Sesi <b>${esc(payload.paket)}</b> pada <b>${dateStr}, ${esc(booking.jam)}</b> (target: ${esc(payload.target.toLowerCase())}). Coach Dedi konfirmasi via WhatsApp.`;
      toast(`Booking berhasil ✓ ${dateStr.split(',')[0]} ${booking.jam}`);

      bookForm.reset();
      curSlot = '';
      dateInput.min = today.toISOString().slice(0, 10);
      renderSlots();
      renderBooked();
    } catch (err) {
      toast(err.message);
      if (err.fields.nama) nama.classList.add('err');
      if (err.fields.wa) wa.classList.add('err');
      if (err.fields.tanggal) tgl.classList.add('err');
      if (err.status === 409 || err.fields.jam) { curSlot = ''; renderSlots(); }
    } finally {
      sending = false; btn.disabled = false;
    }
  });

    // render awal
    renderSlots();
    renderBooked();

    // paket dari tautan /booking?paket=Nama
    const wanted = new URLSearchParams(location.search).get('paket');
    if (wanted) {
      const radio = bookForm.querySelector(`[name="paket"][value="${CSS.escape(wanted)}"]`);
      if (radio) { radio.checked = true; toast(`Paket ${wanted} dipilih. Lengkapi tanggal & jam.`); }
    }
  }

  // pesan sambutan setelah daftar
  const flash = $('#flash');
  if (flash && flash.dataset.msg) toast(flash.dataset.msg);

})();
