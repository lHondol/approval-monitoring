<?php

namespace App\Services;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Models\DrawingTransaction;
use App\Models\DrawingTransactionRejectedFile;
use App\Models\DrawingTransactionStep;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Str;
use Yajra\DataTables\DataTables;

class DrawingTransactionRejectedImageService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createRejectedImages($drawingTransactionId, $drawingTransactionStepId, $filepath) {
        $drawingTransactionRejectedFile = new DrawingTransactionRejectedFile();
        $drawingTransactionRejectedFile->drawing_transaction_id = $drawingTransactionId;
        $drawingTransactionRejectedFile->drawing_transaction_step_id = $drawingTransactionStepId;
        $drawingTransactionRejectedFile->filepath = $filepath;
        $drawingTransactionRejectedFile->save();

        return $drawingTransactionRejectedFile;
    }
}
