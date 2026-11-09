@extends('layouts.site')
@section('title', 'Layanan — ' . config('trainer.name'))
@section('description', 'Personal training, online coaching, dan nutrition plan. Lengkap dengan kalkulator BMI dan jawaban pertanyaan umum.')
@section('script', 'app')

@section('content')
@include('pages._hero', ['eyebrow' => 'Layanan', 'title' => 'Program yang cocok untuk targetmu', 'lead' => 'Pilih satu paket, hitung kebutuhan kalorimu, dan baca jawaban untuk pertanyaan yang paling sering muncul.'])
@include('partials.services', ['noHead' => true])
@include('partials.faq')
@endsection
