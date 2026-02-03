<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reporting\ExportRequest;
use App\Services\ExportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportingController extends Controller
{
    private $exportService;
    public function __construct(ExportService $exportService) {
        $this->exportService = $exportService;
    }

    public function view() {
        return view('reporting.view');
    }

    public function export(ExportRequest $request) {
        $topic = $request->input('topic');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $exportData = $this->exportService->export($topic, $fromDate, $toDate);
        $timestamp = Carbon::now()->timestamp;
        return Excel::download($exportData, "{$topic}-{$timestamp}.xlsx"); 
    }
}
