@extends('layouts.auth')
@section('title', 'Lupa password — ' . config('trainer.name'))

@section('aside') @include('auth._aside') @endsection

@section('card')
  <a class="back" href="{{ route('login') }}">← Kembali masuk</a>
  <h1>Lupa password?</h1>
  <p class="sub">Masukkan email akunmu. Kami kirim tautan untuk membuat password baru.</p>

  @if (session('status')) <div class="note ok">{{ session('status') }}</div> @endif
  @if (config('mail.default') === 'log' && app()->environment('local'))
    <div class="note warn">Mode lokal: email tidak benar-benar terkirim. Buka <b>storage/logs/laravel.log</b> dan cari tautan <i>reset-password</i> di sana.</div>
  @endif

  <form class="af" method="POST" action="{{ route('password.email') }}" data-auth>
    @csrf
    <div class="fld">
      <label for="email">Email</label>
      <div class="inp">
        <svg class="ico"><use href="#i-mail"/></svg>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="kamu@email.com">
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror
    </div>
    <button class="btn-auth" type="submit"><span>Kirim tautan reset</span></button>
  </form>
@endsection
