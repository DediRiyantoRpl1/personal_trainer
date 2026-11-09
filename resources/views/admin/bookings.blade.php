@extends('admin.layout')
@section('title', 'Booking — ' . config('trainer.name'))

@php
  $day  = fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('id');
  $todayStr = now()->toDateString();
  $waiting  = $upcoming->where('status', '!=', 'confirmed')->count();
@endphp

@section('content')
<div class="page-h">
  <div>
    <h1>Booking klien</h1>
    <p class="sub">@if ($waiting) {{ $waiting }} booking menunggu konfirmasi. @else Semua booking mendatang sudah dikonfirmasi. @endif</p>
  </div>
</div>

<div data-filterable>
  <div class="toolbar">
    <div class="chips" role="group" aria-label="Filter status">
      <button type="button" class="chip" data-chip="all" aria-pressed="true">Semua</button>
      <button type="button" class="chip" data-chip="pending" aria-pressed="false">Menunggu</button>
      <button type="button" class="chip" data-chip="confirmed" aria-pressed="false">Dikonfirmasi</button>
    </div>
    <label class="search"><svg class="i"><use href="#i-search"/></svg><input data-search type="search" placeholder="Cari nama, paket, atau nomor" aria-label="Cari booking"></label>
  </div>

  @foreach ([['Akan datang', $upcoming, true, 'Belum ada booking mendatang. Booking baru dari website muncul di sini.'], ['Riwayat (30 terakhir)', $past, false, 'Belum ada riwayat.']] as [$title, $rows, $actions, $emptyText])
  <section class="sect" data-section>
    <div class="sect-h">
      <h2>{{ $title }} <small>(<span data-count>{{ $rows->count() }}</span>)</small></h2>
      @if (! $actions && $rows->isNotEmpty())
        <form method="POST" action="{{ route('admin.bookings.clear') }}" onsubmit="return confirm('Hapus semua riwayat booking (yang tanggalnya sudah lewat)? Tidak bisa dibatalkan.')">
          @csrf @method('DELETE')
          <button class="sm del" type="submit">Hapus riwayat</button>
        </form>
      @endif
    </div>
    <div class="panel">
      @if ($rows->isEmpty())
        <p class="empty">{{ $emptyText }}</p>
      @else
      <table class="tbl stack">
        <thead><tr><th>Tanggal</th><th>Jam</th><th>Nama</th><th>WhatsApp</th><th>Paket</th><th>Target</th><th>Status</th>@if($actions)<th></th>@endif</tr></thead>
        <tbody>
        @foreach ($rows as $b)
          @php $d = $day($b->tanggal); @endphp
          <tr data-item data-status="{{ $b->status }}" data-text="{{ mb_strtolower($b->nama . ' ' . $b->paket . ' ' . $b->wa . ' ' . $b->target) }}">
            <td data-l="Tanggal" class="tgl"><span><b>{{ $d->translatedFormat('D, j M Y') }}</b>@if ($b->tanggal == $todayStr)<small>Hari ini</small>@elseif ($d->isTomorrow())<small>Besok</small>@endif</span></td>
            <td data-l="Jam" class="jam">{{ $b->jam }}</td>
            <td data-l="Nama"><b>{{ $b->nama }}</b></td>
            <td data-l="WhatsApp"><a class="wa" href="{{ $b->waLink() }}" target="_blank" rel="noopener">{{ $b->wa }}</a></td>
            <td data-l="Paket">{{ $b->paket }}</td>
            <td data-l="Target">{{ $b->target }}</td>
            <td data-l="Status"><span class="badge {{ $b->status === 'confirmed' ? 'ok' : '' }}">{{ $b->status === 'confirmed' ? 'Dikonfirmasi' : 'Menunggu' }}</span></td>
            @if ($actions)
            <td class="acts">
              @if ($b->status !== 'confirmed')
                <form method="POST" action="{{ route('admin.confirm', $b) }}">@csrf @method('PATCH')<button class="sm go" type="submit">Konfirmasi</button></form>
              @endif
              <form method="POST" action="{{ route('admin.destroy', $b) }}" onsubmit="return confirm('Hapus booking ini?')">@csrf @method('DELETE')<button class="sm del" type="submit">Hapus</button></form>
            </td>
            @endif
          </tr>
        @endforeach
        </tbody>
      </table>
      <p class="none" data-none hidden>Tidak ada booking yang cocok. Ubah filter atau kata pencarian.</p>
      @endif
    </div>
  </section>
  @endforeach
</div>
@endsection
