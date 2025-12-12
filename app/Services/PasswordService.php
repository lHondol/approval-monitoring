<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

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
}
