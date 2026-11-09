@extends('layouts.site')
@section('title', $post->title . ' — ' . config('trainer.name'))
@section('description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 150))

@section('content')
<article class="section article">
  <a class="back" href="{{ route('blog.index') }}">← Semua artikel</a>
  <h1>{{ $post->title }}</h1>
  <p class="meta">{{ $post->published_at->translatedFormat('j F Y') }} · {{ $post->readingMinutes() }} menit baca · oleh {{ config('trainer.fullname') }}</p>
  @if ($post->image_url)
    <img class="cover" src="{{ $post->image_url }}" alt="" onerror="this.style.display='none'">
  @endif

  <div class="prose">
    @foreach (preg_split('/\R{2,}/', trim($post->body)) as $para)
      @if (str_starts_with($para, '## '))
        <h2>{{ trim(substr($para, 3)) }}</h2>
      @else
        @php($lines = preg_split('/\R/', $para))
        @if (str_starts_with($lines[0], '## '))
          <h2>{{ trim(substr($lines[0], 3)) }}</h2>
          <p>{!! nl2br(e(implode("\n", array_slice($lines, 1)))) !!}</p>
        @else
          <p>{!! nl2br(e($para)) !!}</p>
        @endif
      @endif
    @endforeach
  </div>

  <div class="cta-box">
    <div><h3>Siap mulai?</h3><p>Booking sesi pertama gratis dan susun program yang pas untukmu.</p></div>
    <a class="btn" href="{{ route('booking.page') }}">Booking Sesi Gratis</a>
  </div>

  @if ($related->isNotEmpty())
    <h3 class="rel-title">Artikel lainnya</h3>
    <div class="post-grid small">
      @foreach ($related as $r)
        <a class="post-card" href="{{ route('blog.show', $r) }}">
          <div class="post-body"><small>{{ $r->published_at->translatedFormat('j F Y') }}</small><h3>{{ $r->title }}</h3></div>
        </a>
      @endforeach
    </div>
  @endif
</article>
@endsection
