@extends('layouts.site')
@section('title', 'FAQ — ' . config('trainer.name'))
@section('description', 'Pertanyaan yang sering ditanyakan tentang sesi gratis, paket, jadwal, dan pembayaran.')

@section('content')
@include('pages._hero', ['eyebrow' => 'FAQ', 'title' => 'Pertanyaan yang sering ditanyakan', 'lead' => 'Tidak menemukan jawabannya? Hubungi kami lewat halaman Kontak.'])

<section class="section faq-wrap">
  @foreach ($faq as [$q, $a])
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
@endsection
