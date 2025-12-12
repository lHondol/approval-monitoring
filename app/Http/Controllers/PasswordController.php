<?php

namespace App\Http\Controllers;

use App\Http\Requests\Password\ChangePasswordRequest;
use App\Services\AuthService;
use App\Services\PasswordService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    private $passwordService;
    private $authService;
    public function __construct(PasswordService $passwordService, AuthService $authService) {
        $this->passwordService = $passwordService;
        $this->authService = $authService;
    }

    public function changePasswordForm() {
        return view('password.change');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $changed = $this->passwordService->changePassword($request->current_password, $request->password);

        if (!$changed) {
            return back()->withErrors(['current_password' => 'Current password does not match']);
        }

        $this->authService->logout();

        return redirect()->route('loginForm')->with('passwordChanged', 'Password berhasil diganti, mohon login ulang');
    }
}
