<div class="badge-ico" style="margin-bottom:20px"><svg class="ico"><use href="#i-shield"/></svg></div>
<h2>Area <mark>admin</mark></h2>
<p>Khusus pengelola website {{ config('trainer.name') }}. Akun admin terpisah dari akun klien.</p>
<ul class="shield-list">
  <li><svg class="ico"><use href="#i-key"/></svg><span>Verifikasi 2 langkah (kode 6 angka lewat email)</span></li>
  <li><svg class="ico"><use href="#i-clock"/></svg><span>Sesi berakhir otomatis setelah {{ config('trainer.admin_idle_minutes') }} menit tidak aktif</span></li>
  <li><svg class="ico"><use href="#i-shield"/></svg><span>Percobaan login dibatasi dan semua aktivitas tercatat</span></li>
</ul>
<span class="ipbox"><svg class="ico"><use href="#i-eye"/></svg> IP kamu: {{ request()->ip() }}</span>
