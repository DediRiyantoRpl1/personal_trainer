@php $c = config('trainer.contact'); @endphp
<footer class="site-footer">
  <div class="foot-grid">
    <div>
      <a class="logo" href="{{ route('home') }}"><i>D</i>{{ config('trainer.name') }}</a>
      <p class="foot-about">Personal trainer bersertifikat di {{ config('trainer.city') }}. Program 1-on-1 berbasis data: latihan, nutrisi, dan pendampingan mingguan.</p>
    </div>
    <div>
      <h4>Halaman</h4>
      <ul>
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li><a href="{{ route('about') }}">Tentang</a></li>
        <li><a href="{{ route('services') }}">Layanan</a></li>
        <li><a href="{{ route('booking.page') }}">Booking</a></li>
        <li><a href="{{ route('contact') }}">Kontak</a></li>
        <li><a href="{{ route('blog.index') }}">Blog</a></li>
      </ul>
    </div>
    <div>
      <h4>Kontak</h4>
      <ul>
        <li>{{ $c['address'] }}</li>
        <li><a href="https://wa.me/{{ $c['wa'] }}" target="_blank" rel="noopener">{{ $c['phone'] }}</a></li>
        <li><a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a></li>
        <li>{{ $c['hours'] }}</li>
        <li><a href="https://instagram.com/{{ $c['instagram'] }}" target="_blank" rel="noopener">Instagram {{ '@' . $c['instagram'] }}</a></li>
      </ul>
    </div>
  </div>
  <div class="foot-bottom">
    <span>© {{ date('Y') }} {{ config('trainer.name') }} · Personal Trainer · {{ config('trainer.city') }}</span>
    <span class="foot-legal">
      <a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a>
      <a href="{{ route('privacy') }}">Kebijakan Privasi</a>
      <a href="#top">↑ Atas</a>
    </span>
  </div>
</footer>

<a class="wa-float" href="https://wa.me/{{ $c['wa'] }}?text={{ rawurlencode('Halo Coach Dedi, saya ingin bertanya tentang program latihan.') }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
  <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" fill="currentColor"><path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9.5L4 22v-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></svg>
</a>
