<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Utils\Responses;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $response;
    private $user_service;

    public function __construct()
    {
        $this->response = new Responses();
        $this->user_service = new UserService();
    }

    public function getAdmin()
    {
        return $this->user_service->getAdmin();
    }

    public function getNotifications(Request $request)
    {
        try {
            $notifications = auth()->user()->notifications->take(7);
            $count = auth()->user()->unreadNotifications->count();
            return $this->response->successWithData(['notifications' => $notifications, 'count' => $count]);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function markReadAll()
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();
            return $this->response->success('Marked read all notifications !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function getListUsers()
    {
        try {
            return $this->response->successWithData($this->user_service->getListUsers());
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function getUserByID(Request $request)
    {
        try {
            return $this->response->successWithData($this->user_service->getUserByID($request->id));
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function updateUser(UpdateUserRequest $request)
    {
        try {
            $data_validated = $request->validated();
            if ($user = $this->user_service->updateUser($data_validated)) {
                return $this->response->success('Update success !');
            }
            return $this->response->error('Not your', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function updateProfile(UpdateUserRequest $request)
    {
        try {
            $data_validated = $request->validated();
            if ($user = $this->user_service->updateUser(Arr::except($data_validated, 'avatar'))) {
                $url_image = Arr::has($data_validated,
                    'avatar') ? $data_validated['avatar']->store(Image::DIRECTORY_IMAGES) : null;
                if ($url_image) {
                    $image_ctl = new ImageController();
                    if ($user->avatar) {
                        $image = $image_ctl->update($user->avatar, $url_image);
                    } else {
                        $image = $image_ctl->create($url_image);
                        $user->avatar = $image->id;
                        $user->save();
                    }
                }
                return $this->response->success('Update profile success !');
            }
            return $this->response->error('Not your', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $this->user_service->deleteUser($request->id);
            return $this->response->success('Deleted user success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $data_validated = $request->validated();
            switch ($data_validated['role']) {
                case 'Admin':
                    $data_validated['role'] = 0;
                    break;
                case 'User':
                    $data_validated['role'] = 1;
                    break;
                default:
                    $data_validated['role'] = -1;
                    break;
            }
            $user = $this->user_service->create($data_validated);
            return $this->response->success('Created user success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function deleteMultipleUsers(Request $request)
    {
        try {
            foreach ($request->selected as $id) {
                $this->user_service->deleteUser($id);
            }
            return $this->response->success('Deleted users you selected');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

}
