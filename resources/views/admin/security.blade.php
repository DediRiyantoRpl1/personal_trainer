@extends('admin.layout')
@section('title', 'Keamanan — ' . config('trainer.name'))


@section('content')
<div class="page-h"><div><h1>Keamanan akun</h1><p class="sub">Verifikasi 2 langkah, ganti password, dan riwayat login.</p></div></div>
@if ($errors->any())<div class="errbox">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

@if ($codes)
<div class="codes-box">
  <b>Kode pemulihan — simpan sekarang</b>
  <p class="muted" style="font-size:.9rem">Dipakai kalau email tidak bisa diakses. Tiap kode hanya berlaku sekali dan tidak akan ditampilkan lagi.</p>
  <div class="codes">@foreach ($codes as $c)<code>{{ $c }}</code>@endforeach</div>
  <button type="button" class="sm" onclick="navigator.clipboard.writeText(@js(implode("\n", $codes))).then(()=>this.textContent='Tersalin ✓')">Salin semua</button>
</div>
@endif

<div class="sec-grid">
  {{-- 2FA --}}
  <div class="sec-card">
    <h2>Verifikasi 2 langkah
      @if ($admin->hasTwoFactor()) <span class="badge ok">Aktif</span> @else <span class="badge">Belum aktif</span> @endif
    </h2>

    @if ($admin->hasTwoFactor())
      <p>Setiap login admin meminta kode 6 angka yang dikirim ke email {{ $admin->email }}. Sisa kode pemulihan: <b>{{ count($admin->two_factor_recovery_codes ?? []) }}</b>.</p>
      <form method="POST" action="{{ route('admin.security.2fa.codes') }}" style="display:grid;gap:10px">
        @csrf
        <label>Password saat ini<input type="password" name="current_password" required autocomplete="current-password"></label>
        <button class="sm" type="submit" style="justify-self:start">Buat kode pemulihan baru</button>
      </form>
      <form method="POST" action="{{ route('admin.security.2fa.disable') }}" style="display:grid;gap:10px;border-top:1px solid var(--line);padding-top:14px" onsubmit="return confirm('Matikan verifikasi 2 langkah?')">
        @csrf
        <label>Password saat ini<input type="password" name="current_password" required autocomplete="current-password"></label>
        <button class="btn red" type="submit">Matikan 2FA</button>
      </form>

    @elseif ($setup)
      <p>Kami sudah mengirim kode 6 angka ke <b>{{ $admin->email }}</b>. Masukkan kodenya untuk mengaktifkan. Berlaku 10 menit.</p>
      <form method="POST" action="{{ route('admin.security.2fa.confirm') }}" style="display:grid;gap:10px">
        @csrf
        <label>Kode 6 angka<input name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required autofocus placeholder="123456"></label>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn" type="submit">Aktifkan</button>
        </div>
      </form>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <form method="POST" action="{{ route('admin.security.2fa.start') }}">@csrf <button class="sm" type="submit">Kirim ulang kode</button></form>
        <form method="POST" action="{{ route('admin.security.2fa.cancel') }}">@csrf <button class="sm" type="submit">Batal</button></form>
      </div>

    @else
      <p>Setiap login akan meminta kode 6 angka yang dikirim ke email admin. Tambahkan lapisan keamanan kedua. Walau password bocor, orang lain tetap tidak bisa masuk tanpa akses ke email kamu.</p>
      <form method="POST" action="{{ route('admin.security.2fa.start') }}">@csrf <button class="btn" type="submit">Aktifkan sekarang</button></form>
    @endif
  </div>

  {{-- Ganti password --}}
  <div class="sec-card">
    <h2>Ganti password</h2>
    <p>Minimal 10 karakter. Gabungkan huruf besar-kecil, angka, dan simbol.</p>
    <form method="POST" action="{{ route('admin.security.password') }}" style="display:grid;gap:12px">
      @csrf @method('PUT')
      <label>Password saat ini<input type="password" name="current_password" required autocomplete="current-password"></label>
      <label>Password baru<input type="password" name="password" required minlength="10" autocomplete="new-password"></label>
      <label>Ulangi password baru<input type="password" name="password_confirmation" required minlength="10" autocomplete="new-password"></label>
      <button class="btn" type="submit">Simpan password</button>
    </form>
    @if ($admin->last_login_at)
      <p style="border-top:1px solid var(--line);padding-top:12px">Login terakhir: {{ $admin->last_login_at->translatedFormat('j M Y, H:i') }} dari IP {{ $admin->last_login_ip }}</p>
    @endif
  </div>
</div>

{{-- Riwayat login --}}
<div class="sect-h" style="margin-top:26px">
  <h2>Riwayat login admin</h2>
  @if ($logs->isNotEmpty())
  <form method="POST" action="{{ route('admin.security.logs.clear') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap" onsubmit="return confirm('Hapus semua riwayat login admin? Tindakan ini tidak bisa dibatalkan.')">
    @csrf @method('DELETE')
    <input type="password" name="current_password" required placeholder="Password saat ini" autocomplete="current-password" style="width:auto;min-width:200px">
    <button class="sm del" type="submit">Hapus riwayat</button>
  </form>
  @endif
</div>
<div class="adm-card">
  <table class="tbl">
    <thead><tr><th>Waktu</th><th>Status</th><th>IP</th><th>Perangkat</th><th>Catatan</th></tr></thead>
    <tbody>
    @forelse ($logs as $l)
      <tr>
        <td>{{ $l->created_at->translatedFormat('j M Y, H:i') }}</td>
        <td>@if($l->success)<span class="badge ok">Berhasil</span>@else<span class="badge bad">Gagal</span>@endif</td>
        <td>{{ $l->ip }}</td>
        <td class="ua">{{ $l->device() }}</td>
        <td>{{ $l->note ?? '—' }}</td>
      </tr>
    @empty
      <tr><td colspan="5" class="empty">Belum ada riwayat.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<p class="muted" style="margin-top:10px;font-size:.85rem">Percobaan gagal dari email yang tidak terdaftar sebagai admin juga tercatat di database (tabel <code>login_logs</code>), tapi tidak ditampilkan di sini.</p>
@endsection
