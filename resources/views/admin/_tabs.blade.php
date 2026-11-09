@php $unread = \App\Models\Message::where('dibaca', false)->count(); @endphp
<nav class="adm-tabs">
  <a href="{{ route('admin.bookings') }}" class="{{ request()->routeIs('admin.bookings') ? 'on' : '' }}">Booking</a>
  <a href="{{ route('admin.posts') }}" class="{{ request()->routeIs('admin.posts*') ? 'on' : '' }}">Artikel</a>
  <a href="{{ route('admin.messages') }}" class="{{ request()->routeIs('admin.messages*') ? 'on' : '' }}">Pesan @if($unread)<b class="cnt">{{ $unread }}</b>@endif</a>
  <a href="{{ route('admin.security') }}" class="{{ request()->routeIs('admin.security*') ? 'on' : '' }}">🔐 Keamanan</a>
  <span class="sp"></span>
  <span class="muted" style="font-size:.85rem">{{ auth('admin')->user()->name }}</span>
  <a href="{{ \App\Support\Site::mainUrl() }}" target="_blank">Lihat website ↗</a>
  <form method="POST" action="{{ route('admin.logout') }}">@csrf <button class="sm" type="submit">Keluar</button></form>
</nav>
