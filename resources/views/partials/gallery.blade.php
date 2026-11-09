<section id="galeri" class="section">
  <div class="head reveal"><p class="eyebrow">Galeri</p><h2>Momen latihan &amp; hasil klien</h2><p class="muted">Klik foto untuk memperbesar.</p></div>
  <div class="gallery">
    @foreach ($t['gallery'] as $p)
      <button type="button" class="g-item reveal" data-full="{{ $p['img'] }}" data-alt="{{ $p['alt'] }}" aria-label="Perbesar: {{ $p['alt'] }}">
        <img src="{{ $p['img'] }}" alt="{{ $p['alt'] }}" loading="lazy" onerror="this.closest('.g-item').style.display='none'">
      </button>
    @endforeach
  </div>
</section>

<dialog id="lightbox" class="lightbox">
  <button type="button" class="lb-close" aria-label="Tutup">×</button>
  <img id="lbImg" alt="">
  <p id="lbCap"></p>
</dialog>

@push('scripts')
<script>
(function () {
  var dlg = document.getElementById('lightbox'), img = document.getElementById('lbImg'), cap = document.getElementById('lbCap');
  if (!dlg || !dlg.showModal) return;
  document.querySelectorAll('.g-item').forEach(function (b) {
    b.addEventListener('click', function () {
      img.src = b.dataset.full; img.alt = b.dataset.alt; cap.textContent = b.dataset.alt; dlg.showModal();
    });
  });
  dlg.addEventListener('click', function (e) { if (e.target === dlg || e.target.classList.contains('lb-close')) dlg.close(); });
})();
</script>
@endpush
