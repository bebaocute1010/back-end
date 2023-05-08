<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Image;
use App\Notifications\PostNotification;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Utils\Responses;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    private $post_service;
    private $response;
    private $user_ctl;

    public function __construct() {
        $this->post_service = new PostService();
        $this->response = new Responses();
        $this->user_ctl = new UserController();
    }

    public function getPost(Request $request) {
        try {
            $post = $this->post_service->getPost($request->id);
            $post->url_image = $post->image();
            return $this->response->successWithData($post);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function update(PostUpdateRequest $request) {
        try {
            $data = $request->validated();
            if ($post = $this->post_service->update(Arr::except($data,'image'))) {
                $url_image = Arr::has($data,'image') ? $data['image']->store(Image::DIRECTORY_IMAGES) : null;
                if ($url_image) {
                    $image_ctl = new ImageController();
                    $image = $image_ctl->update($post->image_id, $url_image);
                }
                return $this->response->success('Update post success !');
            }
            return $this->response->error('Not your', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);

        }
    }

    public function create(PostCreateRequest $request) {
        try {
            $data_validated = $request->validated();
            $filename = $data_validated['image']->store(Image::DIRECTORY_IMAGES);
            $image_ctl = new ImageController();
            $image = $image_ctl->create($filename);
            $data_validated['image_id'] = $image->id;
            Arr::except($data_validated, 'image');
            $post = $this->post_service->create(Arr::except($data_validated, 'image'));
            $admins = $this->user_ctl->getAdmin();
            foreach ($admins as $admin) {
                $admin->notify(new PostNotification($post));
            }
            return $this->response->success('Create post success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function delete(Request $request) {
        try {
            if ($this->post_service->delete($request->id)) {
                return $this->response->success('Delete post success !');
            }
            return $this->response->error('Not your', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function deleteMultiple(Request $request) {
        try {
            foreach ($request->selected as $id) {
                if (!$this->post_service->delete($id)) {
                    return $this->response->error("Post #'". $id ."' is not yours", Response::HTTP_BAD_REQUEST);
                }
            }
            return $this->response->success('Deleted posts you selected');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function postsOfUser(Request $request) {
        $index = 0;
        $posts = auth()->user()->posts()->map(function ($post, $index) {
            $index++;
            return [
                'id' => $post->id,
                'index' => $index,
                'name' => $post->name,
                'slug' => $post->slug,
                'url' => $post->image(),
                'content' => $post->content,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ];
        });
        return $this->response->successWithData($posts);
    }

    public function getAllPostsByGuest(Request $request) {
        try {
            $posts = $this->post_service->getAllPosts()->map(function ($post) {
                return [
                    'name' => $post->name,
                    'url' => $post->image(),
                    'content' => $post->content,
                    'created_at' => $post->created_at,
                    'author' => $post->user()->name,
                    'slug' => $post->slug
                ];
            });
             return $this->response->successWithData($posts);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function getPostBySlug(Request $request) {
        try {
            $post = $this->post_service->getPostBySlug($request->slug);
            $post->author = $post->user()->name;
            $post->url = $post->image();
            $post->makeHidden(['id', 'image_id', 'user_id', 'deleted_at', 'updated_at', 'slug']);
            return $this->response->successWithData($post);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }
}
