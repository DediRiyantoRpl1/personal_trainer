@extends('admin.layout')
@section('title', 'Artikel — ' . config('trainer.name'))

@section('content')
<div class="page-h">
  <div>
    <h1>Artikel blog</h1>
    <p class="sub">{{ $posts->where('published_at', '!=', null)->count() }} terbit dari {{ $posts->count() }} artikel. Blog dapat dibuka dari footer website.</p>
  </div>
  <a class="btn" href="{{ route('admin.posts.create') }}"><svg class="i"><use href="#i-plus"/></svg>Artikel baru</a>
</div>

<div data-filterable>
  <div class="toolbar">
    <div class="chips" role="group" aria-label="Filter status">
      <button type="button" class="chip" data-chip="all" aria-pressed="true">Semua</button>
      <button type="button" class="chip" data-chip="published" aria-pressed="false">Terbit</button>
      <button type="button" class="chip" data-chip="draft" aria-pressed="false">Draft</button>
    </div>
    <label class="search"><svg class="i"><use href="#i-search"/></svg><input data-search type="search" placeholder="Cari judul artikel" aria-label="Cari artikel"></label>
  </div>

  <section class="sect" data-section style="margin-top:12px">
    <div class="panel">
      @if ($posts->isEmpty())
        <p class="empty">Belum ada artikel. Klik "Artikel baru" untuk menulis yang pertama.</p>
      @else
      <table class="tbl stack">
        <thead><tr><th>Judul</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
        <tbody>
        @foreach ($posts as $post)
          <tr data-item data-status="{{ $post->isPublished() ? 'published' : 'draft' }}" data-text="{{ mb_strtolower($post->title . ' ' . $post->slug) }}">
            <td class="ttl"><b>{{ $post->title }}</b><small>/blog/{{ $post->slug }}</small></td>
            <td data-l="Status"><span class="badge {{ $post->isPublished() ? 'ok' : 'mute' }}">{{ $post->isPublished() ? 'Terbit' : 'Draft' }}</span></td>
            <td data-l="Tanggal">{{ ($post->published_at ?? $post->created_at)->translatedFormat('j M Y') }}</td>
            <td class="acts">
              @if ($post->isPublished())<a class="sm" href="{{ route('blog.show', $post) }}" target="_blank" rel="noopener">Lihat</a>@endif
              <a class="sm" href="{{ route('admin.posts.edit', $post) }}">Edit</a>
              <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Hapus artikel ini?')">@csrf @method('DELETE')<button class="sm del" type="submit">Hapus</button></form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
      <p class="none" data-none hidden>Tidak ada artikel yang cocok.</p>
      @endif
    </div>
  </section>
</div>
@endsection
