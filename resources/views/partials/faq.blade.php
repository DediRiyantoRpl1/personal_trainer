<section id="faq" class="section faq-wrap">
  <div class="head reveal"><p class="eyebrow">FAQ</p><h2>Pertanyaan yang sering ditanyakan</h2></div>
  @foreach ($t['faq'] as [$q, $a])
    <details class="faq-item reveal">
      <summary>{{ $q }}</summary>
      <p>{{ $a }}</p>
    </details>
  @endforeach
  <div class="cta-box">
    <div><h3>Masih ada pertanyaan?</h3><p>Kirim pesan, kami balas secepatnya.</p></div>
    <a class="btn" href="{{ route('contact') }}">Hubungi Kami</a>
  </div>
</section>
