@extends('admin.layout')
@section('title', 'Ringkasan — ' . config('trainer.name'))

@php
  use Illuminate\Support\Carbon;
  $full = $monthCount >= $quota;
@endphp

@section('content')
<div class="page-h">
  <div>
    <h1>{{ $greeting }}, {{ $first }}</h1>
    <p class="sub">{{ $dateLabel }}</p>
  </div>
  <a class="btn" href="{{ route('admin.posts.create') }}"><svg class="i"><use href="#i-plus"/></svg>Artikel baru</a>
</div>

<section class="panel strip" aria-label="Angka penting">
  <a class="cell" href="{{ route('admin.bookings') }}">
    <b>{{ $todayCount }}<small> / {{ $todaySlots }}</small></b>
    <span>Sesi hari ini</span>
    <em>{{ $todayCount ? 'slot terisi' : 'belum ada yang booking' }}</em>
  </a>
  <a class="cell" href="{{ route('admin.bookings') }}">
    <b>{{ $pendingCount }}</b>
    <span>Menunggu konfirmasi</span>
    <em>{{ $pendingCount ? 'perlu ditindak' : 'semua sudah dikonfirmasi' }}</em>
  </a>
  <div>
    <b>{{ $monthCount }}<small> / {{ $quota }}</small></b>
    <span>Kuota bulan ini</span>
    <div class="meter {{ $full ? 'full' : '' }}" role="img" aria-label="{{ $monthCount }} dari {{ $quota }} kuota terpakai"><i style="width:{{ $quota ? min(100, round($monthCount / $quota * 100)) : 0 }}%"></i></div>
  </div>
  <a class="cell" href="{{ route('admin.messages') }}">
    <b>{{ $unreadCount }}</b>
    <span>Pesan baru</span>
    <em>{{ $unreadCount ? 'belum dibaca' : 'tidak ada yang tertunda' }}</em>
  </a>
</section>

<div class="dash">
  <div class="col">

    <section class="panel" data-tabroot aria-label="Papan jadwal">
      <div class="panel-h">
        <h2>Papan jadwal</h2>
        <div class="seg" role="tablist" data-tabs>
          <button type="button" role="tab" data-tab="today" aria-selected="true">Hari ini</button>
          <button type="button" role="tab" data-tab="tomorrow" aria-selected="false">Besok</button>
        </div>
      </div>
      @foreach ($boards as $key => $bd)
        <div data-pane="{{ $key }}" @if (! $loop->first) hidden @endif>
          @if (count($bd['slots']))
          <div class="board">
            @foreach ($bd['slots'] as $s)
              @php $b = $s['booking']; @endphp
              <div class="slot {{ $b ? 'taken' : 'free' }} {{ $s['state'] }} {{ $b && $b->status !== 'confirmed' ? 'pending' : '' }}">
                <time>{{ $s['jam'] }}</time>
                @if ($b)
                  <span class="who">{{ $b->nama }}</span>
                  <span class="pk">{{ $b->paket }}</span>
                  <span class="st">{{ $b->status === 'confirmed' ? 'Dikonfirmasi' : 'Menunggu' }}</span>
                @else
                  <span class="pk">Kosong</span>
                @endif
              </div>
            @endforeach
          </div>
          <p class="board-note">{{ $bd['date']->locale('id')->translatedFormat('l, j F') }}. Slot yang disorot lime adalah sesi berikutnya.</p>
          @else
            <p class="empty">Tidak ada jam sesi untuk hari ini. Jam sesi diatur di <code>config/trainer.php</code>.</p>
          @endif
        </div>
      @endforeach
    </section>

    <section class="panel" aria-label="Booking 14 hari ke depan">
      <div class="panel-h"><h2>14 hari ke depan <small>tinggi batang = slot terisi</small></h2></div>
      <div class="bars" role="img" aria-label="Jumlah booking per hari selama 14 hari ke depan">
        @foreach ($days as $d)
          <div class="bar {{ $d['n'] ? '' : 'zero' }} {{ $d['today'] ? 'today' : '' }} {{ $d['off'] ? 'off' : '' }}" title="{{ $d['date']->locale('id')->translatedFormat('l, j F') }}: {{ $d['n'] }} dari {{ $d['cap'] }} slot">
            <div class="col"><i style="height:{{ $d['n'] ? max(8, $d['pct']) : 0 }}%"></i></div>
            <div><b>{{ $d['date']->format('j') }}</b><small>{{ mb_substr($d['date']->locale('id')->translatedFormat('D'), 0, 3) }}</small></div>
          </div>
        @endforeach
      </div>
      <p class="chart-foot">Hari Sabtu dan Minggu tampil lebih redup. Arahkan kursor ke batang untuk melihat rinciannya.</p>
    </section>

  </div>

  <div class="col">

    <section class="panel" aria-label="Perlu dikonfirmasi">
      <div class="panel-h"><h2>Perlu dikonfirmasi <small>{{ $pendingCount }}</small></h2></div>
      @if ($pending->isEmpty())
        <p class="empty">Tidak ada booking yang menunggu. Booking baru dari website akan muncul di sini.</p>
      @else
      <ul class="rows">
        @foreach ($pending as $b)
          @php $d = Carbon::parse($b->tanggal)->locale('id'); @endphp
          <li>
            <div class="dt"><b>{{ $d->format('j') }}</b><small>{{ $d->translatedFormat('M') }}</small></div>
            <div>
              <div class="nm">{{ $b->nama }}</div>
              <div class="meta">{{ $b->jam }}, {{ $b->paket }}</div>
            </div>
            <form method="POST" action="{{ route('admin.confirm', $b) }}">@csrf @method('PATCH')<button class="sm go" type="submit">Konfirmasi</button></form>
          </li>
        @endforeach
      </ul>
      @if ($pendingCount > $pending->count())<p class="panel-f"><a href="{{ route('admin.bookings') }}">Lihat semua {{ $pendingCount }} booking</a></p>@endif
      @endif
    </section>

    <section class="panel" aria-label="Pesan terbaru">
      <div class="panel-h"><h2>Pesan terbaru</h2></div>
      @if ($messages->isEmpty())
        <p class="empty">Belum ada pesan. Pesan dari halaman Kontak website akan muncul di sini.</p>
      @else
      <ul class="rows">
        @foreach ($messages as $m)
          <li class="msgrow {{ $m->dibaca ? '' : 'unread' }}">
            <span class="av" aria-hidden="true">{{ mb_strtoupper(mb_substr($m->nama, 0, 1)) }}</span>
            <div>
              <div class="nm">{{ $m->nama }}</div>
              <div class="meta">{{ \Illuminate\Support\Str::limit($m->pesan, 64) }}</div>
            </div>
            <time>{{ $m->created_at->locale('id')->diffForHumans(null, true) }}</time>
          </li>
        @endforeach
      </ul>
      <p class="panel-f"><a href="{{ route('admin.messages') }}">Buka semua pesan</a></p>
      @endif
    </section>

  </div>
</div>
@endsection
