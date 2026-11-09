@extends('layouts.auth')
@section('title', 'Password baru — ' . config('trainer.name'))

@section('aside') @include('auth._aside') @endsection

@section('card')
  <h1>Buat password baru</h1>
  <p class="sub">Pilih password yang kuat dan belum pernah kamu pakai di tempat lain.</p>

  <form class="af" method="POST" action="{{ route('password.update') }}" data-auth>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="fld">
      <label for="email">Email</label>
      <div class="inp">
        <svg class="ico"><use href="#i-mail"/></svg>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" readonly>
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror
    </div>
    @include('auth._password', ['name' => 'password', 'label' => 'Password baru (min. 8 karakter)', 'auto' => 'new-password', 'meter' => true])
    @include('auth._password', ['name' => 'password_confirmation', 'label' => 'Ulangi password baru', 'auto' => 'new-password', 'match' => 'password'])
    <button class="btn-auth" type="submit"><span>Simpan password</span></button>
  </form>
@endsection
