<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\SignupRequest;
use App\Services\UserService;
use App\Utils\Responses;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private $user_service;
    private $response;

    public function __construct()
    {
        $this->user_service = new UserService();
        $this->response     = new Responses();
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return $this->response->unauthorized();
        }
        return $this->response->successWithToken($token);
    }

    public function me()
    {
        $me         = auth()->user();
        $me->avatar = $me->avatar();
        return $this->response->successWithData($me);
    }

    public function signUp(SignupRequest $request)
    {
        try {
            $data_validated = $request->validated();
            $this->user_service->create($data_validated);
            return $this->response->success('Create account success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return $this->response->success('Logout success !');
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }

    public function refresh()
    {
        return $this->response->successWithToken(auth()->refresh());
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $data_validated = $request->validated();
            if (Hash::check(
              $data_validated['password'],
              auth()->user()->password
            )
            ) {
                $this->user_service->updateUser(
                  [
                    'id'       => auth()->id(),
                    'password' => $data_validated['new_password'],
                  ]
                );
                return $this->response->success('Change password success !');
            }
            return $this->response->error(
              'Password not correct !',
              Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $exception) {
            return $this->response->exceptionError($exception);
        }
    }
}
