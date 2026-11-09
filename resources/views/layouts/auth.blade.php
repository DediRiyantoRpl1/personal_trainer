@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('body')
{{-- ikon --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
  <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7.5l8.5 6 8.5-6"/></symbol>
  <symbol id="i-lock" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2.5"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></symbol>
  <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4.5 20c1-3.8 4-5.5 7.5-5.5s6.5 1.7 7.5 5.5"/></symbol>
  <symbol id="i-phone" viewBox="0 0 24 24"><rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M11 18.5h2"/></symbol>
  <symbol id="i-eye" viewBox="0 0 24 24"><path d="M2 12s3.8-7 10-7 10 7 10 7-3.8 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol>
  <symbol id="i-eyeoff" viewBox="0 0 24 24"><path d="M3 3l18 18"/><path d="M10.6 6.1A9.6 9.6 0 0 1 12 5c6.2 0 10 7 10 7a17 17 0 0 1-3.1 4M6.5 6.8C3.8 8.6 2 12 2 12s3.8 7 10 7c1.5 0 2.9-.4 4.1-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></symbol>
  <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2.2 2.2L15.5 10"/></symbol>
  <symbol id="i-key" viewBox="0 0 24 24"><circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M16 7l3 3M14 9l2 2"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
</defs></svg>

<div class="auth @yield('variant')">
  <aside class="auth-aside">
    <a class="logo" href="{{ \App\Support\Site::mainUrl() }}"><i>D</i>{{ config('trainer.name') }}</a>
    <div class="aside-body">@yield('aside')</div>
    <p class="aside-foot">© {{ date('Y') }} {{ config('trainer.name') }} · {{ config('trainer.city') }}</p>
  </aside>

  <main class="auth-main">
    <div class="auth-card">@yield('card')</div>
    @yield('after')
  </main>
</div>

<script src="{{ asset('assets/js/auth.js') }}"></script>
@endsection
