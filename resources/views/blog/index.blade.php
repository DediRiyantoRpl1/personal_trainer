@extends('layouts.site')
@section('title', 'Blog — ' . config('trainer.name'))
@section('description', 'Tips latihan, nutrisi, dan kebiasaan sehat dari Coach Dedi.')

@section('content')
@include('pages._hero', ['eyebrow' => 'Blog', 'title' => 'Tips latihan & hidup sehat', 'lead' => 'Artikel singkat yang bisa langsung kamu praktikkan.'])

<section class="section blog-wrap">
  @if ($posts->isEmpty())
    <p class="muted">Belum ada artikel.</p>
  @else
  <div class="post-grid">
    @foreach ($posts as $post)
      <a class="post-card reveal" href="{{ route('blog.show', $post) }}">
        <div class="post-img">
          @if ($post->image_url)<img src="{{ $post->image_url }}" alt="" loading="lazy" onerror="this.style.display='none'">@endif
        </div>
        <div class="post-body">
          <small>{{ $post->published_at->translatedFormat('j F Y') }} · {{ $post->readingMinutes() }} menit baca</small>
          <h3>{{ $post->title }}</h3>
          <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 130) }}</p>
          <span class="more">Baca selengkapnya →</span>
        </div>
      </a>
    @endforeach
  </div>

  @if ($posts->hasPages())
    <div class="pager">
      @if ($posts->previousPageUrl())<a class="btn line" href="{{ $posts->previousPageUrl() }}">← Lebih baru</a>@endif
      @if ($posts->nextPageUrl())<a class="btn line" href="{{ $posts->nextPageUrl() }}">Lebih lama →</a>@endif
    </div>
  @endif
  @endif
</section>
@endsection
