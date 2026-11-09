<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return view('blog.index', [
            'posts' => Post::published()->latest('published_at')->simplePaginate(6),
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        return view('blog.show', [
            'post'    => $post,
            'related' => Post::published()->where('id', '!=', $post->id)->latest('published_at')->limit(3)->get(),
        ]);
    }
}
