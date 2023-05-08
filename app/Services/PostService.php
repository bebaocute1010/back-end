<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Support\Arr;

class PostService
{
    private $post_repository;

    public function __construct() {
        $this->post_repository = new PostRepository();
    }

    public function getPost($id) {
        return $this->post_repository->getPost($id);
    }

    public function update(array $data) {
        $post = $this->getPost($data['id']);
        if (auth()->id() == $post->user_id) {
            $post->update(Arr::except($data, ['id']));
            return $post;
        }
        return null;
    }

    public function create(array $data) {
        return $this->post_repository->create($data);
    }

    public function delete($id) {
        $post = $this->getPost($id);
        if (auth()->id() == $post->user_id) {
            $post->delete();
            return true;
        }
        return false;
    }

    public function getAllPosts() {
        return $this->post_repository->getAllPosts();
    }

    public function getPostBySlug(string $slug) {
        return $this->post_repository->getPostBySlug($slug);
    }
}
