<section id="booking" class="section booking" style="padding-top:20px">
  <div class="booking-grid">
    <div class="reveal">
      <h2>Yang kamu dapat di sesi gratis</h2>
      <ul class="perks">
        <li>✔ Asesmen postur &amp; mobilitas</li>
        <li>✔ Target &amp; roadmap 12 minggu</li>
        <li>✔ Tanpa kewajiban berlangganan</li>
      </ul>
      <div class="booked-wrap" id="bookedWrap" hidden>
        <p class="mini-label">Booking kamu</p>
        <ul class="booked-list" id="bookedList"></ul>
      </div>
    </div>

    <form id="bookForm" class="book-form reveal" novalidate
          data-slots-url="{{ route('booking.slots') }}"
          data-mine-url="{{ route('booking.mine') }}"
          data-store-url="{{ route('booking.store') }}"
          data-auth="{{ auth()->check() ? '1' : '0' }}"
          data-login-url="{{ route('login') }}"
          data-cancel-url="{{ url('/booking') }}">
      @guest
        <p class="auth-note">🔒 Untuk booking, <a href="{{ route('login') }}">masuk</a> atau <a href="{{ route('register') }}">buat akun</a> dulu (gratis).</p>
      @endguest
      <fieldset>
        <legend>Paket</legend>
        @foreach ($t['services'] as $svc)
          <label class="opt"><input type="radio" name="paket" value="{{ $svc['name'] }}" @checked($loop->first)><span>{{ $svc['name'] }}</span></label>
        @endforeach
      </fieldset>
      <div class="two">
        <label>Nama<input name="nama" value="{{ auth()->user()->name ?? '' }}" required autocomplete="name" maxlength="100"></label>
        <label>WhatsApp<input name="wa" type="tel" value="{{ auth()->user()->wa ?? '' }}" required placeholder="08xx" autocomplete="tel"></label>
      </div>
      <label>Tanggal<input name="tgl" type="date" required></label>
      <div class="slots-section">
        <span class="mini-label">Pilih jam</span>
        <div class="slots" id="slotList"></div>
      </div>
      <label>Target utama
        <select name="goal">
          @foreach ($t['goals'] as $goal)
            <option>{{ $goal }}</option>
          @endforeach
        </select>
      </label>
      <button class="btn" type="submit">Konfirmasi Booking</button>
      <div class="confirm" id="confirm" role="status"></div>
    </form>
  </div>
</section>
