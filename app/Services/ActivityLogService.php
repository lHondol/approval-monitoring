<?php

namespace App\Services;

use App\Models\ActivityLog;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class ActivityLogService
{
    public function getData()
    {
        return DataTables::of(
            ActivityLog::with('user')
                ->select(['id', 'user_id', 'action', 'module', 'description', 'created_at'])
        )
        ->addColumn('user', function ($log) {
            return $log->user->name ?? '-';
        })
        ->editColumn('created_at', function ($log) {
            return $log->created_at->format('Y-m-d H:i:s');
        })
        ->rawColumns(['user'])
        ->make(true);
    }

    public function create($data)
    {
        $log = new ActivityLog();

        $log->id = Uuid::uuid4()->toString();
        $log->user_id = auth()->user()->id;
        $log->action = $data->action;
        $log->module = $data->module;
        $log->description = $data->description ?? null;
        $log->subject_id = $data->subject_id ?? null;
        $log->properties = $data->properties ?? null;

        $log->save();

        return $log;
    }
}