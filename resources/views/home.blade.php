@extends('layouts.site')

@section('script', 'app')

@section('content')
  @include('partials.hero')
  @include('partials.transform')
  @include('partials.services', ['teaser' => true])

  <section class="section">
    <div class="cta-box">
      <div><h3>Siap mulai?</h3><p>Sesi pertama 30 menit gratis, tanpa kewajiban berlangganan.</p></div>
      <a class="btn" href="{{ route('booking.page') }}">Booking Sesi Gratis</a>
    </div>
  </section>
@endsection
