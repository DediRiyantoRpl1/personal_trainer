@php
  $admin   = auth('admin')->user();
  $pending = \App\Models\Booking::where('status', 'pending')->where('tanggal', '>=', now()->toDateString())->count();
  $unread  = \App\Models\Message::where('dibaca', false)->count();
  $brand   = config('trainer.name');
  $initials = collect(preg_split('/\s+/', $brand))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
  $nav = [
    ['admin.home',      'admin.home',       'Ringkasan', 'grid',   null],
    ['admin.bookings',  'admin.bookings*',  'Booking',   'cal',    $pending],
    ['admin.posts',     'admin.posts*',     'Artikel',   'doc',    null],
    ['admin.messages',  'admin.messages*',  'Pesan',     'mail',   $unread],
    ['admin.security',  'admin.security*',  'Keamanan',  'shield', null],
  ];
  $palette = [
    ['label' => 'Ringkasan',       'href' => route('admin.home'),          'kw' => 'dashboard beranda jadwal'],
    ['label' => 'Booking klien',   'href' => route('admin.bookings'),      'kw' => 'jadwal sesi konfirmasi'],
    ['label' => 'Artikel blog',    'href' => route('admin.posts'),         'kw' => 'tulisan'],
    ['label' => 'Tulis artikel baru', 'href' => route('admin.posts.create'), 'kw' => 'buat'],
    ['label' => 'Pesan masuk',     'href' => route('admin.messages'),      'kw' => 'kontak inbox'],
    ['label' => 'Keamanan akun',   'href' => route('admin.security'),      'kw' => 'password 2fa riwayat login'],
    ['label' => 'Buka website',    'href' => \App\Support\Site::mainUrl(), 'kw' => 'publik situs', 'external' => true, 'hint' => 'tab baru'],
    ['label' => 'Keluar',          'href' => '#',                          'kw' => 'logout', 'action' => 'logout'],
  ];
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Admin — ' . $brand)</title>
<script>try{var t=localStorage.getItem('adm-theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.dataset.theme=t}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ @filemtime(public_path('assets/css/admin.css')) }}">
@stack('head')
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.8"/><rect x="14" y="3" width="7" height="5" rx="1.8"/><rect x="14" y="12" width="7" height="9" rx="1.8"/><rect x="3" y="16" width="7" height="5" rx="1.8"/></symbol>
  <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="4.5" width="18" height="16.5" rx="2.6"/><path d="M8 2.5v4M16 2.5v4M3 10h18"/></symbol>
  <symbol id="i-doc" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2.6"/><path d="m3.5 7 8.5 6.5L20.5 7"/></symbol>
  <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3 4.5 6v5.5c0 4.5 3.2 8 7.5 9.5 4.3-1.5 7.5-5 7.5-9.5V6z"/><path d="m9 12 2.2 2.2L15.5 10"/></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/></symbol>
  <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.3 5.3l1.6 1.6M17.1 17.1l1.6 1.6M18.7 5.3l-1.6 1.6M6.9 17.1l-1.6 1.6"/></symbol>
  <symbol id="i-moon" viewBox="0 0 24 24"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/></symbol>
  <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></symbol>
  <symbol id="i-out" viewBox="0 0 24 24"><path d="M10 4H5.5A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20H10M15 8l4 4-4 4M19 12H9"/></symbol>
  <symbol id="i-ext" viewBox="0 0 24 24"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></symbol>
  <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
</svg>

<div class="shell">
  <aside class="side" id="side" aria-label="Menu admin">
    <a class="brand" href="{{ route('admin.home') }}">
      <i>{{ $initials }}</i>
      <span><b>{{ $brand }}</b><small>Panel admin</small></span>
    </a>

    <nav>
      @foreach ($nav as [$route, $pattern, $label, $icon, $count])
        <a href="{{ route($route) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
          <svg class="i"><use href="#i-{{ $icon }}"/></svg>
          <span>{{ $label }}</span>
          @if ($count) <b class="cnt" title="{{ $label === 'Pesan' ? 'belum dibaca' : 'menunggu konfirmasi' }}">{{ $count }}</b> @endif
        </a>
      @endforeach
    </nav>

    <div class="side-foot">
      <a class="ext" href="{{ \App\Support\Site::mainUrl() }}" target="_blank" rel="noopener"><svg class="i"><use href="#i-ext"/></svg>Buka website</a>
      <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">@csrf
        <button class="ext" type="submit"><svg class="i"><use href="#i-out"/></svg>Keluar</button>
      </form>
      <div class="me">
        <span class="av">{{ mb_strtoupper(mb_substr($admin->name, 0, 1)) }}</span>
        <div><b>{{ $admin->name }}</b><small>{{ $admin->email }}</small></div>
      </div>
    </div>
  </aside>
  <div class="scrim" id="scrim"></div>

  <div class="main">
    <header class="topbar">
      <button class="iconbtn menu-btn" type="button" data-drawer aria-label="Buka menu"><svg class="i"><use href="#i-menu"/></svg></button>
      <button class="jump" type="button" data-palette><svg class="i"><use href="#i-search"/></svg><span>Lompat ke halaman</span><kbd>Ctrl K</kbd></button>
      <span class="sp"></span>
      <button class="iconbtn" type="button" data-theme-toggle aria-label="Ganti tema terang atau gelap" title="Ganti tema">
        <svg class="i t-moon"><use href="#i-moon"/></svg><svg class="i t-sun"><use href="#i-sun"/></svg>
      </button>
    </header>

    <main class="page">@yield('content')</main>
  </div>
</div>

<div class="pal" id="pal" role="dialog" aria-modal="true" aria-label="Lompat ke halaman">
  <div class="pal-box">
    <input type="text" placeholder="Ketik nama halaman, mis. booking" aria-label="Cari halaman" autocomplete="off">
    <ul role="listbox"></ul>
  </div>
</div>
<div id="toasts" aria-live="polite"></div>

<script>
  window.__nav = @json($palette);
  @if (session('ok')) window.__flash = @json(session('ok')); @endif
</script>
<script src="{{ asset('assets/js/admin.js') }}?v={{ @filemtime(public_path('assets/js/admin.js')) }}" defer></script>
</body>
</html>
