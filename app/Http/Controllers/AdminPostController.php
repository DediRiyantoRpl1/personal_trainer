<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', ['posts' => Post::latest()->get()]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post()]);
    }

    public function store(Request $request)
    {
        $post = new Post();
        $this->save($request, $post);

        return redirect()->route('admin.posts')->with('ok', 'Artikel disimpan.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $this->save($request, $post);

        return redirect()->route('admin.posts')->with('ok', 'Artikel diperbarui.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('ok', 'Artikel dihapus.');
    }

    private function save(Request $request, Post $post): void
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:150'],
            'slug'      => ['nullable', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:160'],
            'excerpt'   => ['nullable', 'string', 'max:300'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'body'      => ['required', 'string'],
            'published' => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'Slug hanya huruf kecil, angka, dan tanda strip (contoh: tips-olahraga).',
            'image_url.url' => 'Alamat gambar harus berupa URL lengkap (https://...).',
        ]);

        $base = Str::slug($data['slug'] ?: $data['title']) ?: 'artikel';
        $slug = $base;
        $n = 2;
        while (Post::where('slug', $slug)->where('id', '!=', $post->id ?? 0)->exists()) {
            $slug = $base . '-' . $n++;
        }

        $post->fill([
            'title'        => $data['title'],
            'slug'         => $slug,
            'excerpt'      => $data['excerpt'] ?? null,
            'image_url'    => $data['image_url'] ?? null,
            'body'         => $data['body'],
            'published_at' => $request->boolean('published') ? ($post->published_at ?? now()) : null,
        ])->save();
    }
}
