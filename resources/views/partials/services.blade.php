@php $teaser = $teaser ?? false; @endphp
<section id="layanan" class="section services">
  @unless ($noHead ?? false)
  <div class="head reveal"><p class="eyebrow">Layanan</p><h2>Pilih cara berlatihmu</h2></div>
  @endunless
  <div class="cards">
    @foreach ($t['services'] as $svc)
    <article class="svc reveal">
      <div class="svc-img"><img src="{{ $svc['img'] }}" alt="{{ $svc['name'] }}" loading="lazy" onerror="this.style.display='none'"></div>
      <div class="svc-body">
        <div class="ic">{{ $svc['icon'] }}</div>
        <h3>{{ $svc['name'] }}</h3>
        <p>{{ $svc['desc'] }}</p>
        <span>{{ $svc['price'] }}</span>
        @if ($teaser)
          <a class="svc-pick" href="{{ route('services') }}">Lihat detail →</a>
        @else
          <a class="svc-pick" href="{{ route('booking.page', ['paket' => $svc['name']]) }}">Pilih paket →</a>
        @endif
      </div>
    </article>
    @endforeach
  </div>

  @unless ($teaser)
  <div class="calc reveal">
    <div>
      <p class="eyebrow">Alat bantu</p>
      <h3>Hitung BMI &amp; kebutuhan kalorimu</h3>
      <p class="muted">Perkiraan awal menggunakan rumus Mifflin-St Jeor. Hasil lengkap kita bahas di sesi gratis.</p>
    </div>
    <form id="calcForm" class="calc-form" novalidate>
      <label>Berat (kg)<input type="number" name="w" min="30" max="250" value="70" required></label>
      <label>Tinggi (cm)<input type="number" name="h" min="120" max="230" value="170" required></label>
      <label>Usia<input type="number" name="a" min="14" max="90" value="28" required></label>
      <label>Jenis kelamin<select name="s"><option value="m">Pria</option><option value="f">Wanita</option></select></label>
      <label class="wide">Aktivitas<select name="act">
        <option value="1.2">Jarang olahraga</option>
        <option value="1.375" selected>1–3× / minggu</option>
        <option value="1.55">3–5× / minggu</option>
        <option value="1.725">6–7× / minggu</option>
      </select></label>
      <button class="btn wide" type="submit">Hitung</button>
      <div class="result wide" id="calcOut" aria-live="polite"></div>
    </form>
  </div>
  @endunless
</section>
