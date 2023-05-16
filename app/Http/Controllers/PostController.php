<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Notifications\PostNotification;
use App\Services\PostService;
use Illuminate\Http\Request;
use App\Utils\Responses;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    private $post_service;
    private $response;
    private $user_ctl;

    public function __construct()
    {
        $this->post_service = new PostService();
        $this->response     = new Responses();
        $this->user_ctl     = new UserController();
    }

    public function getPost(Request $request)
    {
        try {
            $post = $this->post_service->getPost($request->id);
            return $this->response->successWithData($post);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function update(PostUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            if ($post = $this->post_service->update($data)) {
                return $this->response->success('Update post success !');
            }
            return $this->response->error('Not your',
              Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function create(PostCreateRequest $request)
    {
        try {
            $data_validated = $request->validated();
            $post           = $this->post_service->create($data_validated);
            $admins         = $this->user_ctl->getAdmin();
            foreach ($admins as $admin) {
                $admin->notify(new PostNotification($post));
            }
            return $this->response->success('Create post success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function delete(Request $request)
    {
        try {
            if ($this->post_service->delete($request->id)) {
                return $this->response->success('Delete post success !');
            }
            return $this->response->error('Not your',
              Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            foreach ($request->selected as $id) {
                if (!$this->post_service->delete($id)) {
                    return $this->response->error("Post #'" . $id . "' isn't yours",
                      Response::HTTP_BAD_REQUEST);
                }
            }
            return $this->response->success('Deleted posts you selected');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function postsOfUser(Request $request)
    {
        try {
            return $this->response->successWithData($this->post_service->postOfUser(auth()->id()));
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function getAllPostsByGuest(Request $request)
    {
        try {
            return $this->response->successWithData($this->post_service->getAllPosts());
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function getPostBySlug(Request $request)
    {
        try {
            return $this->response->successWithData($this->post_service->getPostBySlug($request->slug));
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }
}
