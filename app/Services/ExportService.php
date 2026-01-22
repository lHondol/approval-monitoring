<?php

namespace App\Services;

use App\Exports\DrawingTransactionsExport;
use Exception;

class ExportService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function exportDrawingTransaction($fromDate, $toDate) {
        return new DrawingTransactionsExport($fromDate, $toDate);
    }

    public function export($topic, $fromDate, $toDate) {
        switch ($topic) {
            case 'drawing_transaction':
                return $this->exportDrawingTransaction($fromDate, $toDate);
            default:
                throw new Exception("Topic not exists", 1);
        }
    }
}
