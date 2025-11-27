<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function loginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $email = $request->email;
        $password = $request->password;
        $rememberMe = (!!$request->remember_me) ?? false;

        $attemptOk = $this->authService->login($email, $password, $rememberMe);

        if ($attemptOk) return redirect()->route('dashboard');

        return back()->withErrors('Invalid email or password');
    }

    public function logout(Request $request) {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loginForm');
    }
}
