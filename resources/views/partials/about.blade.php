<section id="tentang" class="section about">
  <div class="about-card reveal">
    <div class="about-photo">
      <img src="{{ $t['about']['photo'] }}" alt="Foto Coach Dedi" loading="lazy">
    </div>
    <h3>{{ $t['fullname'] }}</h3>
    <p>Certified Personal Trainer · Nutrition Coach</p>
    <ul class="certs">
      @foreach ($t['about']['certs'] as $cert)
        <li>{{ $cert }}</li>
      @endforeach
    </ul>
  </div>
  <div class="about-text reveal">
    <p class="eyebrow">Tentang saya</p>
    <h2>Pendekatan yang <span>personal</span>, bukan template.</h2>
    <p>Saya mulai melatih setelah mengalami sendiri proses turun 28 kg dan membangun kembali kebiasaan hidup sehat. Karena itu saya paham: yang sulit bukan latihannya, tapi konsistensinya.</p>
    <p>Setiap klien memulai dengan asesmen postur, mobilitas, dan gaya hidup. Dari sana saya susun program yang realistis untuk jadwal dan cedera masa lalumu.</p>
    <div class="steps">
      @foreach ($t['about']['steps'] as [$no, $title, $text])
        <div><b>{{ $no }}</b><h4>{{ $title }}</h4><p>{{ $text }}</p></div>
      @endforeach
    </div>
  </div>
</section>
