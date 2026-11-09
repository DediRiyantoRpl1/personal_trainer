@extends('layouts.app')

@section('body')
<div class="progress" aria-hidden="true"><i id="progressBar"></i></div>

@include('partials.header')

<main>
  @yield('content')
</main>

@include('partials.footer')

<div class="toasts" id="toasts" aria-live="polite"></div>
<div id="flash" data-msg="{{ session('welcome') ?? session('toast') }}" hidden></div>

@php($js = trim($__env->yieldContent('script')) ?: 'common')
<script src="{{ asset('assets/js/' . $js . '.js') }}?v={{ @filemtime(public_path('assets/js/' . $js . '.js')) }}"></script>
@stack('scripts')
@endsection
