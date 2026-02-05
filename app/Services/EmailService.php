<?php

namespace App\Services;

use App\Mail\DistributedMail;
use App\Mail\FinalizedMail;
use App\Mail\NeedReviseMail;
use App\Mail\RejectionMail;
use App\Mail\WaitingApprovalMail;
use App\Mail\WaitingFinalizeMail;
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

    public function sendRequestPrereleaseSoApprovalGeneral($transactionId, $so_number, $permissions) {
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }

        $users = $users->get();

        foreach ($users as $user) {
            $approvalUrl = config('app.url') . "/prerelease-so-transactions/approval/{$transactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRequestPrereleaseSoApprovalSalesArea($transactionId, $areaId, $so_number) {
        $permissions = ['sales_area_approve_prerelease_so_transaction'];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }

        $users->whereHas('areas', function ($query) use ($areaId) {
            $query->where('areas.id', $areaId);
        });

        $users = $users->get();

        foreach ($users as $user) {
            $approvalUrl = config('app.url') . "/prerelease-so-transactions/approval/{$transactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRequestPrereleaseSoFinalized($transactionId, $so_number) {
        $permissions = ['mkt_staff_finalize_prerelease_so_transaction'];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }

        $users = $users->get();

        foreach ($users as $user) {
            $approvalUrl = config('app.url') . "/prerelease-so-transactions/approval/{$transactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new WaitingFinalizeMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendNoticePrereleaseSoFinalized($transactionId, $so_number, $permissions) {
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }

        $users = $users->get();

        foreach ($users as $user) {
            $transactionUrl = config('app.url') . "/prerelease-so-transactions/detail/{$transactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new FinalizedMail($transactionUrl, $name, $so_number));
        }
    }

    public function sendRequestRevisePrereleaseSoTransaction($transactionId, $so_number) {
        $permissions = [
            'revise_prerelease_so_transaction'
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        foreach ($users as $user) {
            $reviseUrl = config('app.url') . "/prerelease-so-transactions/revise/{$transactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new NeedReviseMail($reviseUrl, $name, $so_number));
        }
    }

    public function sendRequestApproval1DrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'first_approve_drawing_transaction'
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
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRequestApproval2DrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'second_approve_drawing_transaction'
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
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRequestApprovalBOMDrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'bom_approve_distributed_drawing_transaction'
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
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRequestApprovalCostingDrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'costing_approve_distributed_drawing_transaction'
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
            Mail::to($email)->send(new WaitingApprovalMail($approvalUrl, $name, $so_number));
        }
    }

    public function sendRejectNoticeDrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'bom_approve_distributed_drawing_transaction'
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        foreach ($users as $user) {
            $transactionUrl = config('app.url') . "/drawing-transactions/detail/{$drawingTransactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new RejectionMail($transactionUrl, $name, $so_number));
        }
    }

    public function sendDistributedNoticeDrawingTransaction($drawingTransactionId, $so_number) {
        $permissions = [
            'view_distributed_drawing_transaction'
        ];
        
        $users = User::query();
        
        foreach ($permissions as $perm) {
            $users->whereHas('roles.permissions', function ($query) use ($perm) {
                $query->where('name', $perm);
            });
        }
        
        $users = $users->get();

        foreach ($users as $user) {
            $transactionUrl = config('app.url') . "/drawing-transactions/detail/{$drawingTransactionId}";
            $name = $user->name;
            $email = $user->email;
            Mail::to($email)->send(new DistributedMail($transactionUrl, $name, $so_number));
        }
    }

    public function sendRequestReviseDrawingTransaction($drawingTransactionId, $so_number) {
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
            Mail::to($email)->send(new NeedReviseMail($reviseUrl, $name, $so_number));
        }
    }
}
