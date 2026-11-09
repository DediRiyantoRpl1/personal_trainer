<section id="transformasi" class="section transform">
  <div class="head reveal"><p class="eyebrow">Transformasi</p><h2>Hasil nyata, <span>diukur</span>.</h2></div>
  <div class="ba-wrap reveal">
    <div class="ba" id="ba" style="--pos:50%">
      <div class="ba-pane after">
        <img src="{{ $t['transform']['after']['img'] }}" alt="Hasil setelah 16 minggu" loading="lazy">
        <div class="ba-stat"><small>SESUDAH · minggu 16</small><b>{{ $t['transform']['after']['weight'] }}</b><span>{{ $t['transform']['after']['fat'] }}</span></div>
      </div>
      <div class="ba-pane before">
        <img src="{{ $t['transform']['before']['img'] }}" alt="Sebelum program" loading="lazy">
        <div class="ba-stat"><small>SEBELUM</small><b>{{ $t['transform']['before']['weight'] }}</b><span>{{ $t['transform']['before']['fat'] }}</span></div>
      </div>
      <div class="handle"><span>⇆</span></div>
      <input type="range" min="0" max="100" value="50" id="baRange" aria-label="Geser untuk membandingkan sebelum dan sesudah">
    </div>
    <p class="caption">Geser untuk membandingkan · {{ $t['transform']['caption'] }}</p>
  </div>

  <div class="testi reveal">
    <div class="slides" id="slides">
      @foreach ($t['testimonials'] as $q)
      <blockquote>
        <p>“{{ $q['quote'] }}”</p>
        <cite>
          <img class="avatar" src="{{ $q['avatar'] }}" alt="" loading="lazy" onerror="this.style.display='none'">
          <span>{{ $q['who'] }}</span>
        </cite>
      </blockquote>
      @endforeach
    </div>
    <div class="dots" id="dots"></div>
  </div>
</section>
