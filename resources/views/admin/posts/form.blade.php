@extends('admin.layout')
@section('title', ($post->exists ? 'Edit' : 'Tulis') . ' artikel — ' . config('trainer.name'))

@section('content')
<div class="page-h"><div><h1>{{ $post->exists ? 'Edit artikel' : 'Artikel baru' }}</h1><p class="sub">Pisahkan paragraf dengan baris kosong. Awali baris dengan <code>## </code> untuk subjudul.</p></div></div>

<form class="adm-form" method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
  @csrf
  @if ($post->exists) @method('PUT') @endif

  @if ($errors->any())
    <div class="errbox">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
  @endif

  <label>Judul
    <input name="title" value="{{ old('title', $post->title) }}" required maxlength="150">
  </label>
  <label>Slug (alamat URL) <small class="muted">— kosongkan untuk dibuat otomatis dari judul</small>
    <input name="slug" value="{{ old('slug', $post->slug) }}" maxlength="160" placeholder="contoh: tips-olahraga-pagi">
  </label>
  <label>Ringkasan <small class="muted">— tampil di daftar artikel (maks. 300 karakter)</small>
    <textarea name="excerpt" rows="2" maxlength="300">{{ old('excerpt', $post->excerpt) }}</textarea>
  </label>
  <label>Gambar sampul (URL) <small class="muted">— opsional, alamat lengkap https://...</small>
    <input name="image_url" value="{{ old('image_url', $post->image_url) }}" maxlength="500" placeholder="https://...">
  </label>
  <label>Isi artikel
    <textarea name="body" rows="16" required>{{ old('body', $post->body) }}</textarea>
    <small class="muted">Pisahkan paragraf dengan satu baris kosong. Awali baris dengan <code>## </code> untuk membuat subjudul.</small>
  </label>
  <label style="display:flex;gap:10px;align-items:center;font-weight:600">
    <input type="checkbox" name="published" value="1" style="width:auto" @checked(old('published', $post->exists ? $post->isPublished() : true))>
    Terbitkan (hilangkan centang untuk menyimpan sebagai draft)
  </label>

  <div class="acts">
    <button class="btn" type="submit">Simpan</button>
    <a class="btn line" href="{{ route('admin.posts') }}">Batal</a>
  </div>
</form>
@endsection
