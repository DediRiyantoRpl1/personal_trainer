@extends('layouts.site')
@section('title', 'Syarat & Ketentuan — ' . config('trainer.name'))

@section('content')
@include('pages._hero', ['eyebrow' => 'Legal', 'title' => 'Syarat & Ketentuan'])
<section class="section article"><div class="prose">
  <p><em>Terakhir diperbarui: {{ now()->translatedFormat('j F Y') }}</em></p>
  <h2>1. Layanan</h2>
  <p>{{ config('trainer.name') }} menyediakan layanan personal training, online coaching, dan nutrition plan. Program disusun berdasarkan informasi yang kamu berikan, sehingga hasil dapat berbeda pada tiap orang.</p>
  <h2>2. Booking dan pembatalan</h2>
  <p>Booking dilakukan melalui website setelah masuk ke akun. Kamu dapat membatalkan booking dari bagian "Booking kamu". Mohon membatalkan atau mengubah jadwal sedini mungkin agar slot dapat dipakai orang lain.</p>
  <h2>3. Kondisi kesehatan</h2>
  <p>Kamu bertanggung jawab menyampaikan kondisi kesehatan, cedera, atau keterbatasan yang relevan sebelum berlatih. Informasi di website ini, termasuk kalkulator BMI dan kalori, bersifat umum dan bukan saran medis. Konsultasikan dengan dokter bila ada kondisi medis.</p>
  <h2>4. Pembayaran</h2>
  <p>Biaya sesuai paket yang disepakati dan dikonfirmasi melalui WhatsApp. Sesi pertama berupa konsultasi gratis tanpa kewajiban berlangganan.</p>
  <h2>5. Akun</h2>
  <p>Jaga kerahasiaan password akunmu. Kami berhak menonaktifkan akun yang disalahgunakan.</p>
  <h2>6. Perubahan ketentuan</h2>
  <p>Ketentuan ini dapat diperbarui sewaktu-waktu. Perubahan berlaku sejak dipublikasikan di halaman ini.</p>
  <h2>7. Kontak</h2>
  <p>Pertanyaan tentang ketentuan ini dapat disampaikan melalui halaman <a href="{{ route('contact') }}">Kontak</a>.</p>
</div></section>
@endsection
