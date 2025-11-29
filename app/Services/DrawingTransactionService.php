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

class DrawingTransactionService
{
    private $pdfService;
    /**
     * Create a new class instance.
     */
    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    private function renderActionButtons($row) {
        return '<a href="' . route('drawingTransactionDetailForm', $row->id) . '" 
                class="ui button customButton">
                    Detail
                </a>';
    }

    public function getData() {
        return DataTables::of(DrawingTransaction::select([
            'id',
            'customer_name',
            'so_number',
            'po_number',
            'created_at',
            'distributed_at',
            'status',
            'as_additional_data',
        ])->orderBy('created_at', 'desc'))
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->editColumn('status', function($row) {
            if ($row->as_additional_data)
                return "<div class='flex gap-3'><span class='ui green label'>Additional Data</span> {$row->status}</div>";
            return $row->status;
        })
        ->editColumn('created_at', function($row) {
            return Carbon::parse($row->created_at)->format('d M Y H:i:s');
        })
        ->editColumn('distributed_at', function($row) {
            if ($row->distributed_at)
                return Carbon::parse($row->distributed_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function getSteps($drawingTransactionId) {
        return DrawingTransactionStep::where('drawing_transaction_id', $drawingTransactionId)
            ->with(['user', 'rejected_file'])
            ->get();
    }

    public function getDetail($id) {
        $drawingTransaction = DrawingTransaction::where('id', $id)->first();
        return $drawingTransaction;
    }

    public function create($data) {
        $drawingTransaction = new DrawingTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $drawingTransaction->id =$uuid;

        $drawingTransaction->customer_name = $data->customer_name;
        $drawingTransaction->po_number = $data->po_number;

        $status = StatusDrawingTransaction::WAITING_1ST_APPROVAL;
        $drawingTransaction->status = $status->value;

        if (isset($data->description))
            $drawingTransaction->description = $data->description; 

        if (isset($data->as_additional_data))
            $drawingTransaction->as_additional_data = !!$data->as_additional_data;

        $mergedFilePath = $this->mergePdf(
            $data->files, 
            $uuid,
            $status->value
        );

        $drawingTransaction->filepath = $mergedFilePath;
        $drawingTransaction->save();

        $this->createStep($drawingTransaction, ActionDrawingTransactionStep::UPLOAD);
    }

    public function mergePdf($files, $drawingTransactionId, $status) {
        $status = Str::slug($status, '_');

        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$drawingTransactionId}_{$timestamp}_{$status}.pdf";

        return $this->pdfService->mergeDrawingPdf($files, $newFileName);
    }

    public function createRejectedImages($drawingTransactionId, $drawingTransactionStepId, $filepaths) {
        foreach ($filepaths as $filepath) {
            $drawingTransactionRejectedFile = new DrawingTransactionRejectedFile();
            $drawingTransactionRejectedFile->drawing_transaction_id = $drawingTransactionId;
            $drawingTransactionRejectedFile->drawing_transaction_step_id = $drawingTransactionStepId;
            $drawingTransactionRejectedFile->filepath = $filepath;
            $drawingTransactionRejectedFile->save();
        }
    }

    public function createStep($drawingTransaction, $action, $reason = null) {
        $drawingTransactionStep = new DrawingTransactionStep();
        $drawingTransactionStep->drawing_transaction_id = $drawingTransaction->id;
        $drawingTransactionStep->done_by_user = auth()->user()->id;
        $drawingTransactionStep->done_at = $drawingTransaction->updated_at;
        $drawingTransactionStep->action_done = $action;
        $drawingTransactionStep->reason = $reason;
        $drawingTransactionStep->save();
    }

    public function approval($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->next($data);
    }
}
