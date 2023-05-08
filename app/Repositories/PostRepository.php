<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function getPost($id) {
        $post = Post::find($id);
        return $post;
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
        return Post::where(['slug' => $slug])->first();
    }
}
