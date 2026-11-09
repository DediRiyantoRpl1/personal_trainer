@extends('admin.layout')
@section('title', 'Pesan — ' . config('trainer.name'))

@section('content')
<div class="page-h">
  <div>
    <h1>Pesan masuk</h1>
    <p class="sub">@if ($unread->count()) {{ $unread->count() }} pesan belum dibaca. @else Semua pesan sudah dibaca. @endif</p>
  </div>
</div>

<div data-filterable>
  <div class="toolbar">
    <label class="search"><svg class="i"><use href="#i-search"/></svg><input data-search type="search" placeholder="Cari nama, email, atau isi pesan" aria-label="Cari pesan"></label>
  </div>

  @foreach ([['Pesan baru', $unread, 'Tidak ada pesan baru.'], ['Riwayat (30 terakhir)', $read, 'Belum ada riwayat.']] as [$title, $rows, $emptyText])
  <section class="sect" data-section>
    <div class="sect-h">
      <h2>{{ $title }} <small>(<span data-count>{{ $rows->count() }}</span>)</small></h2>
      @if ($rows->isNotEmpty() && $title !== 'Pesan baru')
        <form method="POST" action="{{ route('admin.messages.clear') }}" onsubmit="return confirm('Hapus semua riwayat pesan (yang sudah dibaca)? Tidak bisa dibatalkan.')">
          @csrf @method('DELETE')
          <button class="sm del" type="submit">Hapus riwayat</button>
        </form>
      @endif
    </div>
    <div class="panel">
      @forelse ($rows as $m)
        <div class="msg {{ $m->dibaca ? '' : 'new' }}" data-item data-text="{{ mb_strtolower($m->nama . ' ' . $m->email . ' ' . $m->pesan) }}">
          <span class="av" aria-hidden="true">{{ mb_strtoupper(mb_substr($m->nama, 0, 1)) }}</span>
          <div>
            <div class="msg-h">
              <b>{{ $m->nama }} @unless($m->dibaca)<span class="badge new">Baru</span>@endunless</b>
              <small>{{ $m->created_at->translatedFormat('D, j M Y, H:i') }}</small>
            </div>
            <div class="from">{{ $m->email }}@if($m->telp), {{ $m->telp }}@endif</div>
            <p>{{ $m->pesan }}</p>
            <div class="acts">
              <a class="sm" href="mailto:{{ $m->email }}?subject={{ rawurlencode('Re: pesan dari website') }}">Balas email</a>
              @if ($m->telp)
                @php($n = preg_replace('/^0/', '62', preg_replace('/\D/', '', $m->telp)))
                <a class="sm" href="https://wa.me/{{ $n }}" target="_blank" rel="noopener">WhatsApp</a>
              @endif
              @unless ($m->dibaca)
                <form method="POST" action="{{ route('admin.messages.read', $m) }}">@csrf @method('PATCH')<button class="sm go" type="submit">Tandai dibaca</button></form>
              @endunless
              <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" onsubmit="return confirm('Hapus pesan ini?')">@csrf @method('DELETE')<button class="sm del" type="submit">Hapus</button></form>
            </div>
          </div>
        </div>
      @empty
        <p class="empty">{{ $emptyText }}</p>
      @endforelse
      @if ($rows->isNotEmpty()) <p class="none" data-none hidden>Tidak ada pesan yang cocok dengan pencarian.</p> @endif
    </div>
  </section>
  @endforeach
</div>
@endsection
