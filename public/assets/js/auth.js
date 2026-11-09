/* JS halaman masuk / daftar / admin: tanpa library */
(() => {
  'use strict';
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  /* tampil/sembunyikan password + peringatan Caps Lock */
  $$('[data-pass]').forEach(input => {
    const wrap = input.closest('.fld');
    const eye  = $('[data-eye]', wrap);
    const caps = $('.caps', wrap);

    if (eye) eye.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      eye.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
      $('use', eye).setAttribute('href', show ? '#i-eyeoff' : '#i-eye');
      input.focus();
    });

    const capsCheck = e => {
      if (!caps || typeof e.getModifierState !== 'function') return;
      caps.hidden = !e.getModifierState('CapsLock');
    };
    input.addEventListener('keydown', capsCheck);
    input.addEventListener('keyup', capsCheck);
    input.addEventListener('blur', () => { if (caps) caps.hidden = true; });
  });

  /* indikator kekuatan password */
  $$('[data-meter]').forEach(input => {
    const box = document.getElementById(input.dataset.meter);
    if (!box) return;
    const label = $('span', box);
    const names = ['Kekuatan password', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
    input.addEventListener('input', () => {
      const v = input.value;
      let score = 0;
      if (v.length >= 8) score++;
      if (v.length >= 12) score++;
      if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
      if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;
      if (v.length && v.length < 8) score = Math.min(score, 1);
      if (!v.length) score = 0;
      box.className = 'meter' + (score ? ' s' + score : '');
      label.textContent = names[score];
    });
  });

  /* cek konfirmasi password sama */
  $$('[data-match]').forEach(input => {
    const other = document.getElementById(input.dataset.match);
    const hint  = $('[data-match-hint]', input.closest('.fld'));
    if (!other || !hint) return;
    const check = () => {
      if (!input.value) { hint.hidden = true; input.classList.remove('good', 'bad'); return; }
      const ok = input.value === other.value;
      hint.hidden = false;
      hint.className = 'hint ' + (ok ? 'ok' : 'no');
      hint.textContent = ok ? '✓ Password cocok' : 'Belum sama dengan password di atas';
      input.classList.toggle('good', ok);
      input.classList.toggle('bad', !ok);
    };
    input.addEventListener('input', check);
    other.addEventListener('input', check);
  });

  /* kode OTP: hanya angka (kecuali kode pemulihan berbentuk ab12-cd34), kirim otomatis saat 6 angka */
  const otp = $('[data-otp]');
  if (otp) {
    otp.addEventListener('input', () => {
      const raw = otp.value.trim();
      if (/^[0-9\s]*$/.test(raw)) {
        const d = raw.replace(/\D/g, '').slice(0, 6);
        otp.value = d;
        if (d.length === 6 && otp.form && !otp.form.dataset.sent) otp.form.requestSubmit();
      }
    });
  }

  /* tombol loading saat kirim + cegah klik ganda */
  $$('form[data-auth]').forEach(form => {
    form.addEventListener('submit', () => {
      if (form.dataset.sent) return;
      form.dataset.sent = '1';
      const btn = $('.btn-auth', form);
      if (btn) {
        btn.classList.add('busy');
        btn.disabled = true;
        const t = $('span', btn);
        if (t) t.textContent = 'Memproses…';
      }
    });
  });
  // tombol "kembali" di browser: pulihkan form
  window.addEventListener('pageshow', e => {
    if (!e.persisted) return;
    $$('form[data-auth]').forEach(f => {
      delete f.dataset.sent;
      const b = $('.btn-auth', f);
      if (b) { b.classList.remove('busy'); b.disabled = false; }
    });
  });

  /* hitung mundur saat akun terkunci sementara */
  const lock = $('[data-lockout]');
  if (lock) {
    let left = parseInt(lock.dataset.lockout, 10) || 0;
    const form = lock.closest('form');
    const btn  = $('.btn-auth', form);
    const txt  = btn && $('span', btn);
    const original = txt ? txt.textContent : '';
    if (left > 0 && btn) {
      btn.disabled = true;
      const tick = () => {
        if (left <= 0) { btn.disabled = false; txt.textContent = original; return; }
        const m = Math.floor(left / 60), s = String(left % 60).padStart(2, '0');
        txt.textContent = `Coba lagi dalam ${m}:${s}`;
        left--;
        setTimeout(tick, 1000);
      };
      tick();
    }
  }

  /* testimoni berganti otomatis di kolom kiri */
  const quote = $('#quote');
  if (quote && quote.dataset.quotes && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    let list = [];
    try { list = JSON.parse(quote.dataset.quotes); } catch (e) {}
    if (list.length > 1) {
      let i = 0;
      const bq = $('blockquote', quote), who = $('.quote-who span', quote), img = $('.quote-who img', quote);
      setInterval(() => {
        quote.classList.add('out');
        setTimeout(() => {
          i = (i + 1) % list.length;
          bq.textContent = '“' + list[i].quote + '”';
          who.textContent = list[i].who;
          if (img && list[i].avatar) img.src = list[i].avatar;
          quote.classList.remove('out');
        }, 400);
      }, 6000);
    }
  }
})();
