<?php

namespace App\Repositories;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Support\Facades\App;

class PostRepository
{
    public function getPost($id) {
        $post            = Post::find($id);
        $post->url_image = $post->url_image();
        return $post;
    }

    public function postOfUser($user_id) {
        return Post::where(['user_id' => $user_id, 'deleted_at' => null])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    public function update($id, array $data) {
        return Post::find($id)->update($data);
    }

    public function create(array $data) {
        return Post::create($data);
    }

    public function getAllPosts() {
        return Post::whereNull('deleted_at')->get();
    }

    public function getPostBySlug(string $slug) {
        $post         = Post::where(['slug' => $slug])->first();
        $post->url    = $post->url_image();
        $post->author = $post->user()->name;
        return $post;
    }
}
