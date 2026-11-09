<section id="beranda" class="hero">
  <div class="hero-text">
    <p class="pill"><b>●</b> Slot bulan ini tersisa <span id="slotLeft">{{ $slotLeft }}</span></p>
    <h1>Tubuh baru dimulai dari <mark>satu sesi</mark> yang tepat.</h1>
    <p class="lead">Program 1-on-1 berbasis data: latihan, nutrisi, dan akuntabilitas mingguan. Saya dampingi sampai targetmu tercapai — bukan sekadar menghitung repetisi.</p>
    <div class="cta-row">
      <a href="{{ route('booking.page') }}" class="btn">Booking Sesi Gratis</a>
      <a href="#transformasi" class="btn line">Lihat Hasil Klien →</a>
    </div>
    <ul class="proof">
      <li><strong data-count="320">0</strong><span>klien terbantu</span></li>
      <li><strong data-count="9">0</strong><span>tahun pengalaman</span></li>
      <li><strong data-count="4.9" data-dec="1">0</strong><span>rating klien</span></li>
    </ul>
  </div>
  <div class="hero-art">
    <div class="blob"></div>
    <div class="ring r1"></div><div class="ring r2"></div>
    <div class="coach-photo">
      <img src="{{ $t['hero']['photo'] }}" alt="Coach {{ $t['fullname'] }}" loading="eager">
    </div>
    <div class="float f1"><b>-12 kg</b><span>dalam 12 minggu</span></div>
    <div class="float f2"><b>1-on-1</b><span>tatap muka &amp; online</span></div>
  </div>
</section>
