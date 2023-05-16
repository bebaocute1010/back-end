<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private $user_repository;

    public function __construct()
    {
        $this->user_repository = new UserRepository();
    }

    public function create(array $data)
    {
        return $this->user_repository->create($data);
    }

    public function getAdmin()
    {
        return $this->user_repository->getAdmin();
    }

    public function getListUsers()
    {
        $users           = $this->user_repository->getAll();
        $index           = 0;
        $users_converted = $users->map(function ($user, $index) {
            switch ($user->role) {
                case 0:
                    $role_text = 'Admin';
                    break;
                case 1:
                    $role_text = 'User';
                    break;
                default:
                    $role_text = 'unknow';
            }
            $index++;
            return [
              'id'         => $user->id,
              'index'      => $index,
              'name'       => $user->name,
              'email'      => $user->email,
              'role'       => $role_text,
              'created_at' => $user->created_at,
              'updated_at' => $user->updated_at,
            ];
        });
        return $users_converted;
    }

    public function getUserByID($id)
    {
        $user = $this->user_repository->getUserByID($id);
        switch ($user->role) {
            case 0:
                $user->role = 'Admin';
                break;
            case 1:
                $user->role = 'User';
                break;
            default:
                $user->role = 'unknow';
                break;
        }
        return $user;
    }

    public function updateUser(array $data)
    {
        return $this->user_repository->updateUser($data);
    }

    public function deleteUser($id)
    {
        return $this->user_repository->deleteUser($id);
    }
}
