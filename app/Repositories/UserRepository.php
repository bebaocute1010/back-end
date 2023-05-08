<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data) {
        return User::create($data);
    }

    public function getAdmin() {
        return User::where('role', 0)->get();
    }

    public function getAll() {
        return User::orderBy('created_at', 'desc')->get();
    }

    public function getUserByID($id) {
        return User::find($id);
    }

    public function updateUser(array $data) {
        $user = User::find($data['id']);
        $user->update($data);
        return $user;
    }

    public function deleteUser($id) {
        return User::find($id)->delete();
    }
}
