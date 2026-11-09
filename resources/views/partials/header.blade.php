<header class="site-header">
  <a class="logo" href="{{ route('home') }}"><i>D</i>{{ config('trainer.name') }}</a>
  <nav class="nav" aria-label="Navigasi utama">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Tentang</a>
    <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">Layanan</a>
    <a href="{{ route('booking.page') }}" class="{{ request()->routeIs('booking.page') ? 'active' : '' }}">Booking</a>
    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a>
  </nav>
  <div class="auth-box">
    @auth
      <span class="who">Hai, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
      <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="abtn">Keluar</button></form>
    @else
      <a href="{{ route('register') }}" class="abtn ghost">Daftar</a>
      <a href="{{ route('login') }}" class="abtn">Masuk</a>
    @endauth
  </div>
  <button class="nav-toggle" aria-label="Buka menu" aria-expanded="false"><span></span><span></span></button>
</header>
