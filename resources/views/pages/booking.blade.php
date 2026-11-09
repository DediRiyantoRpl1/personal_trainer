@extends('layouts.site')
@section('title', 'Booking — ' . config('trainer.name'))
@section('description', 'Booking sesi pertama gratis 30 menit. Pilih paket, tanggal, dan jam yang nyaman.')
@section('script', 'app')

@section('content')
@include('pages._hero', ['eyebrow' => 'Booking', 'title' => 'Sesi pertama gratis.', 'lead' => '30 menit asesmen & konsultasi tanpa komitmen. Pilih paket, tanggal, dan jam yang nyaman.'])
@include('partials.booking')
@endsection
