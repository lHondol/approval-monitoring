<?php

namespace App\Http\Controllers;

use App\Http\Requests\Password\ChangePasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Http\Requests\Password\SendResetLinkRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\PasswordService;
use Carbon\Carbon;
use DB;
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

    public function forgotPasswordForm()
    {
        return view('password.forgot');
    }

    public function sendResetLink(SendResetLinkRequest $request) {
        $email = $request->email;
        $this->passwordService->sendResetLink($email);
        return back()->with('sentResetLink', 'Password reset link terkirim, silahkan check email');
    }

    public function showResetForm(Request $request)
    {
        return view('password.reset', ['token' => $request->token, 'email' => $request->email]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->passwordService->resetPassword(
            $request->token,
            $request->email,
            $request->password
        );

        if ($status == -1)
            return back()->withErrors(['email' => 'Token tidak valid atau email salah.']);
        else if ($status == 0)
            return back()->withErrors(['email' => 'Token sudah kadaluarsa.']);

        return redirect()->route('loginForm')
            ->with('passwordChanged', 'Password berhasil diganti, silakan login ulang.');
    }
}
