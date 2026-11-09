@extends('layouts.auth')
@section('title', 'Masuk Admin — ' . config('trainer.name'))
@section('variant', 'auth--admin')

@section('aside') @include('admin.auth._aside') @endsection

@section('card')
  <div class="badge-ico"><svg class="ico"><use href="#i-lock"/></svg></div>
  <h1>Masuk admin</h1>
  <p class="sub">Gunakan akun admin. Akun klien tidak bisa masuk di sini.</p>

  @if (session('status')) <div class="note ok">{{ session('status') }}</div> @endif

  <form class="af" method="POST" action="{{ route('admin.login.submit') }}" data-auth>
    @csrf
    <div class="fld">
      <label for="email">Email admin</label>
      <div class="inp">
        <svg class="ico"><use href="#i-mail"/></svg>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@email.com">
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror
    </div>

    @include('auth._password', ['name' => 'password', 'label' => 'Password', 'auto' => 'current-password'])

    <button class="btn-auth" type="submit"><span>Masuk</span></button>
    @if (session('lockout')) <i data-lockout="{{ session('lockout') }}" hidden></i> @endif
  </form>

  <p class="alt"><a href="{{ \App\Support\Site::mainUrl() }}">← Ke website utama</a></p>
@endsection
