<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function changePassword($currentPassword, $newPassword) {

        $user = auth()->user();

        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->password = bcrypt($newPassword);
        $user->save();

        return true;
    }

    public function sendResetLink($email)
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send email with token (example)
        Mail::raw(
            "Klik link berikut untuk reset password Anda:\n" .
            url('/reset-password/' . $token . '?email=' . urlencode($email)),
            function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password');
            }
        );
    }
}
