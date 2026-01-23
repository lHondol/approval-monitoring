<?php

namespace App\Exports;

use App\Models\DrawingTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DrawingTransactionsExport implements FromCollection, WithHeadings
{
    protected $fromDate;
    protected $toDate;

    public function __construct($fromDate = null, $toDate = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
        $from = $this->fromDate ? Carbon::parse($this->fromDate)->format('Y-m-d') : null;
        $to   = $this->toDate   ? Carbon::parse($this->toDate)->format('Y-m-d') : null;
        
        return DrawingTransaction::with('customer')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                $latestSteps = $transaction->steps
                    ->groupBy('action_done')
                    ->map(fn($steps) => $steps->sortByDesc('done_at')->first());
                
                return [
                    'Customer Name'       => $transaction->customer->name ?? '',
                    'SO Number'           => $transaction->so_number ?? '',
                    'PO Number'           => $transaction->po_number,
                    'Description'         => $transaction->description,
                    'Created At'          => $transaction->created_at->format('Y-m-d H:i:s'),
                    
                    // Approval details (latest only)
                    'Upload Done At'     => optional($latestSteps['Upload'] ?? null)?->done_at?->format('Y-m-d H:i:s'),
                    'Approve 1 Done At'  => optional($latestSteps['Approve - 1'] ?? null)?->done_at?->format('Y-m-d H:i:s'),
                    'Approve 2 Done At'  => optional($latestSteps['Approve - 2'] ?? null)?->done_at?->format('Y-m-d H:i:s'),
                    
                    'Distributed At'      => optional($transaction->distributed_at)->format('Y-m-d H:i:s'),
                    'Status'              => $transaction->status,
                    'As Additional Data'  => $transaction->as_additional_data ? 'Yes' : 'No',
                    'As Revision Data'    => $transaction->as_revision_data ? 'Yes' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'SO Number',
            'PO Number',
            'Description',
            'Created At',
            'Upload Done At',
            'Approve 1 Done At',
            'Approve 2 Done At',
            'Distributed At',
            'Status',
            'As Additional Data',
            'As Revision Data'
        ];
    }
}
