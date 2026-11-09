@extends('layouts.site')
@section('title', 'Kebijakan Privasi — ' . config('trainer.name'))

@section('content')
@include('pages._hero', ['eyebrow' => 'Legal', 'title' => 'Kebijakan Privasi'])
<section class="section article"><div class="prose">
  <p><em>Terakhir diperbarui: {{ now()->translatedFormat('j F Y') }}</em></p>
  <h2>Data yang kami kumpulkan</h2>
  <p>Saat kamu mendaftar, booking, atau mengirim pesan, kami menyimpan nama, email, nomor WhatsApp, serta detail booking (paket, tanggal, jam, dan target latihan). Password disimpan dalam bentuk terenkripsi (hash), bukan teks asli.</p>
  <h2>Untuk apa data dipakai</h2>
  <p>Data dipakai untuk mengelola jadwal, menghubungimu mengenai sesi, dan membalas pesan. Kami tidak menjual datamu kepada pihak lain.</p>
  <h2>Layanan pihak ketiga</h2>
  <p>Website ini memuat font dari Google Fonts dan gambar dari Unsplash, sehingga alamat IP-mu dapat tercatat oleh layanan tersebut sesuai kebijakan masing-masing.</p>
  <h2>Cookie</h2>
  <p>Kami memakai cookie sesi yang diperlukan agar login dan booking berfungsi. Kami tidak memakai cookie iklan.</p>
  <h2>Hak kamu</h2>
  <p>Kamu dapat meminta data akunmu diperbarui atau dihapus dengan menghubungi kami lewat halaman <a href="{{ route('contact') }}">Kontak</a>.</p>
</div></section>
@endsection
