<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;

class ActivityLogController extends Controller
{
    private $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function view()
    {
        return view('activity-log.view');
    }

    public function getData()
    {
        return $this->activityLogService->getData();
    }
}