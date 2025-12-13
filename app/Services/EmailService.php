<?php

namespace App\Services;

use App\Mail\NeedReviseMail;
use App\Mail\WaitingApprovalMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendRequestApproval1DrawingTransaction($drawingTransactionId) {
        $permissions = [
            'first_approve_drawing_transaction',
            'reject_drawing_transaction',
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        info("users", [$users]);

        foreach ($users as $user) {
            $approvalUrl = config('app.url') . "/drawing-transactions/approval/{$drawingTransactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name));
        }
    }

    public function sendRequestApproval2DrawingTransaction($drawingTransactionId) {
        $permissions = [
            'second_approve_drawing_transaction',
            'reject_drawing_transaction',
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        foreach ($users as $user) {
            $approvalUrl = config('app.url') . "/drawing-transactions/approval/{$drawingTransactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name));
        }
    }

    public function sendRequestReviseDrawingTransaction($drawingTransactionId) {
        $permissions = [
            'revise_drawing_transaction'
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        foreach ($users as $user) {
            $reviseUrl = config('app.url') . "/drawing-transactions/revise/{$drawingTransactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new NeedReviseMail($reviseUrl, $name));
        }
    }
}
