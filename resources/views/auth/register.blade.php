@extends('layouts.auth')
@section('title', 'Daftar — ' . config('trainer.name'))

@section('aside') @include('auth._aside') @endsection

@section('card')
  <a class="back" href="{{ route('home') }}">← Kembali ke beranda</a>
  <h1>Buat akunmu</h1>
  <p class="sub">Gratis. Cukup sekali daftar untuk booking sesi kapan saja.</p>

  <form class="af" method="POST" action="{{ route('register') }}" data-auth>
    @csrf
    <div class="fld">
      <label for="name">Nama lengkap</label>
      <div class="inp">
        <svg class="ico"><use href="#i-user"/></svg>
        <input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" maxlength="100" placeholder="Nama kamu">
      </div>
      @error('name')<p class="err">{{ $message }}</p>@enderror
    </div>

    <div class="fld">
      <label for="email">Email</label>
      <div class="inp">
        <svg class="ico"><use href="#i-mail"/></svg>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="kamu@email.com">
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror
    </div>

    <div class="fld">
      <label for="wa">WhatsApp</label>
      <div class="inp">
        <svg class="ico"><use href="#i-phone"/></svg>
        <input id="wa" type="tel" name="wa" value="{{ old('wa') }}" required autocomplete="tel" placeholder="08xx xxxx xxxx" inputmode="tel">
      </div>
      @error('wa')<p class="err">{{ $message }}</p>@enderror
    </div>

    @include('auth._password', ['name' => 'password', 'label' => 'Password (min. 8 karakter)', 'auto' => 'new-password', 'meter' => true])
    @include('auth._password', ['name' => 'password_confirmation', 'label' => 'Ulangi password', 'auto' => 'new-password', 'match' => 'password'])

    <label class="chk">
      <input type="checkbox" name="terms" value="1" required @checked(old('terms'))>
      <span>Saya setuju dengan <a class="lnk" href="{{ route('terms') }}" target="_blank">Syarat &amp; Ketentuan</a> dan <a class="lnk" href="{{ route('privacy') }}" target="_blank">Kebijakan Privasi</a>.</span>
    </label>
    @error('terms')<p class="err">{{ $message }}</p>@enderror

    <button class="btn-auth" type="submit"><span>Buat akun</span></button>
  </form>

  <p class="alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
