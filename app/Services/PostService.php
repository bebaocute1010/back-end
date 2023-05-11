<?php

namespace App\Services;

use App\Http\Controllers\ImageController;
use App\Models\Image;
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

    public function postOfUser($user_id) {
        return $this->post_repository->postOfUser($user_id)->map(function (
            $post,
            $index
        ) {
            $index++;
            return [
                'id'         => $post->id,
                'index'      => $index,
                'name'       => $post->name,
                'slug'       => $post->slug,
                'url'        => $post->url_image(),
                'content'    => $post->content,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        });;
    }

    public function update(array $data) {
        $post = $this->getPost($data['id']);
        if (auth()->id() == $post->user_id) {
            $url_image = Arr::has($data, 'image')
                ? $data['image']->store(Image::DIRECTORY_IMAGES) : null;
            $this->post_repository->update($post->id,
                Arr::except($data, ['id', 'image']));
            if ($url_image) {
                $image_ctl = new ImageController();
                $image     = $image_ctl->update($post->image_id, $url_image);
            }
            return $post;
        }
        return null;
    }

    public function create(array $data) {
        $filename         = $data['image']->store(Image::DIRECTORY_IMAGES);
        $image_ctl        = new ImageController();
        $image            = $image_ctl->create($filename);
        $data['image_id'] = $image->id;
        return $this->post_repository->create(Arr::except($data, 'image'));
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
        return $this->post_repository->getAllPosts()->map(function ($post) {
            return [
                'name'       => $post->name,
                'url'        => $post->url_image(),
                'content'    => $post->content,
                'created_at' => $post->created_at,
                'author'     => $post->user()->name,
                'slug'       => $post->slug,
            ];
        });
    }

    public function getPostBySlug(string $slug) {
        return $this->post_repository->getPostBySlug($slug)->makeHidden([
            'id', 'image_id', 'user_id', 'deleted_at', 'updated_at', 'slug',
        ]);
    }
}
