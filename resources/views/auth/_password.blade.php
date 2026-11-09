@php
  $id    = $id ?? $name;
  $meter = $meter ?? false;
@endphp
<div class="fld">
  <label for="{{ $id }}">{{ $label }}</label>
  <div class="inp">
    <svg class="ico"><use href="#i-lock"/></svg>
    <input id="{{ $id }}" type="password" name="{{ $name }}" data-pass required
           autocomplete="{{ $auto }}" placeholder="••••••••"
           @if($meter) data-meter="meter-{{ $id }}" @endif
           @if(!empty($match)) data-match="{{ $match }}" @endif>
    <button type="button" class="eye" data-eye aria-label="Tampilkan password"><svg class="ico"><use href="#i-eye"/></svg></button>
  </div>
  <p class="caps" hidden>⇪ Caps Lock sedang aktif</p>
  @if($meter)<div class="meter" id="meter-{{ $id }}"><i></i><span>Kekuatan password</span></div>@endif
  @if(!empty($match))<p class="hint" data-match-hint hidden></p>@endif
  @error($name)<p class="err">{{ $message }}</p>@enderror
</div>
