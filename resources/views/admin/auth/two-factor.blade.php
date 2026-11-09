@extends('layouts.auth')
@section('title', 'Verifikasi 2 langkah — ' . config('trainer.name'))
@section('variant', 'auth--admin')

@section('aside') @include('admin.auth._aside') @endsection

@section('card')
  <div class="badge-ico"><svg class="ico"><use href="#i-key"/></svg></div>
  <h1>Verifikasi 2 langkah</h1>
  <p class="sub">Kami mengirim kode 6 angka ke <b>{{ $maskedEmail }}</b>. Cek inbox (dan folder spam), lalu masukkan kodenya di sini. Berlaku 10 menit.</p>
  @if (session('otp_failed')) <div class="note">Email gagal dikirim. Coba “Kirim ulang kode”, atau pakai kode pemulihan.</div> @endif
  @if (session('otp_sent')) <div class="note ok">Kode baru sudah dikirim.</div> @endif

  <form class="af" method="POST" action="{{ route('admin.2fa.submit') }}" data-auth>
    @csrf
    <div class="fld otp">
      <label for="code">Kode verifikasi</label>
      <div class="inp">
        <input id="code" name="code" required autofocus autocomplete="one-time-code" inputmode="numeric" maxlength="12" placeholder="••••••" data-otp>
      </div>
      <p class="hint">Email tidak masuk? Masukkan salah satu kode pemulihan (contoh: <b>ab12-cd34</b>). Tiap kode hanya berlaku sekali.</p>
      @error('code')<p class="err">{{ $message }}</p>@enderror
    </div>
    <button class="btn-auth" type="submit"><span>Verifikasi</span></button>
    @if (session('lockout')) <i data-lockout="{{ session('lockout') }}" hidden></i> @endif
  </form>

  <form method="POST" action="{{ route('admin.2fa.resend') }}" style="text-align:center;margin-top:10px">
    @csrf
    <button type="submit" class="alt" style="background:none;border:0;padding:0;cursor:pointer;text-decoration:underline;color:inherit">Kirim ulang kode</button>
  </form>

  @if (auth('admin')->check())
    <form method="POST" action="{{ route('admin.logout') }}" style="text-align:center;margin-top:10px">
      @csrf
      <button type="submit" class="alt" style="background:none;border:0;padding:0;cursor:pointer;text-decoration:underline;color:inherit">Keluar dari admin</button>
    </form>
  @else
    <p class="alt"><a href="{{ route(config('trainer.admin_domain') ? 'admin.login' : 'login') }}">← Mulai dari awal</a></p>
  @endif
@endsection
