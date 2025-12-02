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

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('drawing-transaction.datatables.actions', compact('data'));
    }

    public function renderStatusColor($status) {
        return match ($status) {
          StatusDrawingTransaction::WAITING_1ST_APPROVAL->value  => "teal",
          StatusDrawingTransaction::WAITING_2ND_APPROVAL->value   => "orange",
          StatusDrawingTransaction::REVISE_NEEDED->value   => "yellow",
          StatusDrawingTransaction::DISTRIBUTED->value   => "purple",
        };
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
            $renderStatusColor = $this->renderStatusColor($row->status);
            if ($row->as_additional_data)
                return "<div class='flex gap-3 whitespace-nowrap'>
                            <span class='ui green label'>Additional Data</span> 
                            <span class='ui {$renderStatusColor} label'>{$row->status}</span>
                        </div>";
            return "<span class='ui {$renderStatusColor} label'>{$row->status}</span>";
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

        if (isset($data->as_additional_data)) {
            $drawingTransaction->as_additional_data = !!$data->as_additional_data;
            $drawingTransaction->additional_data_note = $data->additional_data_note;
        }

        $mergedFilePath = $this->mergePdf(
            $data->files, 
            $uuid,
            $status->value
        );

        $drawingTransaction->filepath = $mergedFilePath;
        $drawingTransaction->save();

        $this->createStep($drawingTransaction, ActionDrawingTransactionStep::UPLOAD);

        return $drawingTransaction;
    }

    public function mergePdf($files, $drawingTransactionId, $status) {
        $status = Str::slug($status, '_');

        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$drawingTransactionId}_{$timestamp}_{$status}.pdf";

        return $this->pdfService->mergeDrawingPdf($files, $newFileName);
    }

    public function createRejectedImages($drawingTransactionId, $drawingTransactionStepId, $filepath) {
        $drawingTransactionRejectedFile = new DrawingTransactionRejectedFile();
        $drawingTransactionRejectedFile->drawing_transaction_id = $drawingTransactionId;
        $drawingTransactionRejectedFile->drawing_transaction_step_id = $drawingTransactionStepId;
        $drawingTransactionRejectedFile->filepath = $filepath;
        $drawingTransactionRejectedFile->save();

        return $drawingTransactionRejectedFile;
    }

    public function createStep($drawingTransaction, $action, $reason = null) {
        $drawingTransactionStep = new DrawingTransactionStep();
        $drawingTransactionStep->drawing_transaction_id = $drawingTransaction->id;
        $drawingTransactionStep->done_by_user = auth()->user()->id;
        $drawingTransactionStep->done_at = $drawingTransaction->updated_at;
        $drawingTransactionStep->action_done = $action;
        $drawingTransactionStep->reason = $reason;
        $drawingTransactionStep->save();

        return $drawingTransactionStep;
    }

    public function approve($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->next($data);
    }

    public function reject($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->reject($data);
    }

    public function revise($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();

        $drawingTransaction->customer_name = $data->customer_name;
        $drawingTransaction->po_number = $data->po_number;

        $status = StatusDrawingTransaction::WAITING_1ST_APPROVAL;
        $drawingTransaction->status = $status->value;

        if (isset($data->description))
            $drawingTransaction->description = $data->description;


        if (isset($data->files)) {
            $mergedFilePath = $this->mergePdf(
                $data->files, 
                $drawingTransaction->id,
                $status->value
            );

            $drawingTransaction->filepath = $mergedFilePath;
        }

        $drawingTransaction->save();

        $this->createStep($drawingTransaction, ActionDrawingTransactionStep::UPLOAD_REVISED);

        return $drawingTransaction;
    }
}
