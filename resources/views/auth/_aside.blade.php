<h2>Latihan yang <mark>terukur</mark>, hasil yang terasa.</h2>
<p>Masuk untuk booking sesi 1-on-1, melihat jadwalmu, dan mengatur ulang kapan saja dari perangkat apa pun.</p>
<div class="chips">
  <span class="chip"><svg class="ico"><use href="#i-check"/></svg> Sesi pertama gratis</span>
  <span class="chip"><svg class="ico"><use href="#i-check"/></svg> Jadwal fleksibel</span>
  <span class="chip"><svg class="ico"><use href="#i-check"/></svg> Program personal</span>
</div>
@php $quotes = array_values(config('trainer.testimonials', [])); $q = $quotes[0] ?? null; @endphp
@if ($q)
<div class="quote" id="quote" style="margin-top:28px" data-quotes="{{ json_encode($quotes, JSON_UNESCAPED_UNICODE) }}">
  <blockquote>“{{ $q['quote'] }}”</blockquote>
  <div class="quote-who">
    @if (!empty($q['avatar']))<img src="{{ $q['avatar'] }}" alt="" width="40" height="40">@endif
    <span>{{ $q['who'] }}</span>
  </div>
</div>
@endif
