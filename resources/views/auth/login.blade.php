@extends('layouts.auth')
@section('title', 'Masuk — ' . config('trainer.name'))

@section('aside') @include('auth._aside') @endsection

@section('card')
  <a class="back" href="{{ route('home') }}">← Kembali ke beranda</a>
  <h1>Selamat datang kembali</h1>
  <p class="sub">Masuk untuk booking sesi dan melihat jadwalmu.</p>

  @if (session('status')) <div class="note ok">{{ session('status') }}</div> @endif

  <form class="af" method="POST" action="{{ route('login') }}" data-auth>
    @csrf
    <div class="fld">
      <label for="email">Email</label>
      <div class="inp">
        <svg class="ico"><use href="#i-mail"/></svg>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="kamu@email.com">
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror
    </div>

    @include('auth._password', ['name' => 'password', 'label' => 'Password', 'auto' => 'current-password'])

    <div class="row">
      <label class="chk"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
      <a href="{{ route('password.request') }}">Lupa password?</a>
    </div>

    <button class="btn-auth" type="submit"><span>Masuk</span></button>
    @if (session('lockout')) <i data-lockout="{{ session('lockout') }}" hidden></i> @endif
  </form>

  <p class="alt">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
@endsection
