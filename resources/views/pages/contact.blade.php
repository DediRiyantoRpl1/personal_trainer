@extends('layouts.site')
@section('title', 'Kontak — ' . config('trainer.name'))
@section('description', 'Hubungi Coach Dedi lewat WhatsApp, email, atau kirim pesan langsung.')

@section('content')
@include('pages._hero', ['eyebrow' => 'Kontak', 'title' => 'Mari ngobrol soal targetmu', 'lead' => 'Kirim pesan atau langsung chat lewat WhatsApp. Biasanya dibalas dalam 1×24 jam.'])

<section class="section contact-grid">
  <div class="contact-info reveal">
    <ul>
      <li><b>📍 Lokasi</b><span>{{ $c['address'] }}</span></li>
      <li><b>📞 WhatsApp</b><a href="https://wa.me/{{ $c['wa'] }}" target="_blank" rel="noopener">{{ $c['phone'] }}</a></li>
      <li><b>✉️ Email</b><a href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a></li>
      <li><b>🕒 Jam layanan</b><span>{{ $c['hours'] }}</span></li>
      <li><b>📷 Instagram</b><a href="https://instagram.com/{{ $c['instagram'] }}" target="_blank" rel="noopener">{{ '@' . $c['instagram'] }}</a></li>
    </ul>
    <iframe class="map" src="{{ $c['map'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi"></iframe>
  </div>

  <form class="book-form contact-form reveal" method="POST" action="{{ route('contact.store') }}" novalidate>
    @csrf
    <h3>Kirim pesan</h3>
    <div class="two">
      <label>Nama<input name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" required maxlength="100" autocomplete="name"></label>
      <label>Email<input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required autocomplete="email"></label>
    </div>
    <label>WhatsApp <small class="muted">(opsional)</small><input type="tel" name="telp" value="{{ old('telp', auth()->user()->wa ?? '') }}" placeholder="08xx" autocomplete="tel"></label>
    <label>Pesan<textarea name="pesan" rows="5" required maxlength="2000">{{ old('pesan') }}</textarea></label>
    {{-- jebakan bot: disembunyikan dari manusia --}}
    <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>

    @if ($errors->any())
      <div class="form-errors">
        @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
      </div>
    @endif
    <button class="btn" type="submit">Kirim Pesan</button>
  </form>
</section>
@endsection
