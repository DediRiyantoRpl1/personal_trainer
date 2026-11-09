@extends('layouts.site')
@section('title', 'Tentang — ' . config('trainer.name'))
@section('description', 'Kenali Coach Dedi: sertifikasi, pendekatan melatih, dan galeri sesi latihan.')

@section('content')
@include('pages._hero', ['eyebrow' => 'Tentang', 'title' => 'Kenali ' . config('trainer.fullname'), 'lead' => 'Personal trainer bersertifikat di ' . config('trainer.city') . '. Latihan, nutrisi, dan pendampingan yang disesuaikan untukmu.'])
@include('partials.about')
@include('partials.gallery')
@endsection
