<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository {
    public function createUser($email, $password, $name) {
        $user = new User();
        $user['email'] = $email;
        $user['password'] = $password;
        $user['name'] = $name;
        $user->save();
        return $user;
    }

    public function getUserById($userId) {
        return User::query()->where('id', '=', $userId)->first();
    }

    public function getUserByEmail($email) {
        return User::query()->where('email', '=', $email)->first();
    }
}