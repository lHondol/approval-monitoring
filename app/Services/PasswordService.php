<?php

namespace App\Services;

use App\Models\User;
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
            url('/password/reset/' . $token . '?email=' . urlencode($email)),
            function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password');
            }
        );
    }

    public function resetPassword($token, $email, $password) {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return -1;
        }

        // Optional: Token expiry (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return 0;
        }

        // Reset password
        User::where('email', $email)->update([
            'password' => bcrypt($password)
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return 1;
    }
}
