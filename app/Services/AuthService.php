<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function login($email, $password, $rememberMe)
    {
        return Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $rememberMe);
    }

    public function logout() {
        Auth::logout();
    }
}
