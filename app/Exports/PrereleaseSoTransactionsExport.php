<?php

namespace App\Exports;

use App\Models\PrereleaseSoTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrereleaseSoTransactionsExport implements FromCollection, WithHeadings
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

        return PrereleaseSoTransaction::with(['customer', 'steps'])
            ->when($from, fn ($q) =>
                $q->whereDate('created_at', '>=', $from)
            )
            ->when($to, fn ($q) =>
                $q->whereDate('created_at', '<=', $to)
            )
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($transaction) {

                // --- helpers (LOCAL, SAFE) ---
                $nextStep = function ($prev, $current) {
                    if (!$current) return null;
                    if (!$prev) return $current;

                    return Carbon::parse($current)
                        ->greaterThanOrEqualTo($prev)
                            ? $current
                            : null;
                };

                $days = function ($from, $to) {
                    if (!$from || !$to) {
                        return 0; // always numeric for Excel
                    }

                    $from = $from instanceof Carbon
                        ? $from->copy()->startOfDay()
                        : Carbon::parse($from)->startOfDay();

                    $to = $to instanceof Carbon
                        ? $to->copy()->startOfDay()
                        : Carbon::parse($to)->startOfDay();

                    if ($from->equalTo($to)) {
                        return '0';
                    }

                    return (string) $from->diffInDays($to);
                };

                $fmt = fn ($d) =>
                    $d ? Carbon::parse($d)->format('Y-m-d H:i:s') : null;

                // --- steps ---
                $steps = $transaction->steps
                    ->groupBy('action_done')
                    ->map(fn ($s) => $s->sortByDesc('done_at')->first());

                $uploadAt = optional($steps['Upload'] ?? null)?->done_at;

                $salesAreaAt = $nextStep(
                    $uploadAt,
                    optional($steps['Approve - Sales Area'] ?? null)?->done_at
                );

                $rndDrawingAt = $nextStep(
                    $salesAreaAt,
                    optional($steps['Approve - RnD Drawing'] ?? null)?->done_at
                );

                $rndBomAt = $nextStep(
                    $rndDrawingAt,
                    optional($steps['Approve - RnD BOM'] ?? null)?->done_at
                );

                $accountingAt = $nextStep(
                    $rndBomAt,
                    optional($steps['Approve - Accounting'] ?? null)?->done_at
                );

                $itAt = $nextStep(
                    $accountingAt,
                    optional($steps['Approve - IT'] ?? null)?->done_at
                );

                $leadingTimeDay = $days(
                    $transaction->created_at,
                    Carbon::now()
                );

                return [
                    'Customer Name'        => $transaction->customer->name ?? '',
                    'Area'                => $transaction->area->name ?? '',
                    'SO Number'            => $transaction->so_number ?? '',
                    'PO Number'            => $transaction->po_number,
                    'Description'          => $transaction->description,
                    'Created At'           => $fmt($transaction->created_at),
                
                    'Upload Done At'       => $fmt($uploadAt),
                
                    'Sales Area Approved'  => $fmt($salesAreaAt),
                    'Sales Area Day'       => $days($uploadAt, $salesAreaAt),
                
                    'RnD Drawing Approved' => $fmt($rndDrawingAt),
                    'RnD Drawing Day'      => $days($salesAreaAt, $rndDrawingAt),
                
                    'RnD BOM Approved'     => $fmt($rndBomAt),
                    'RnD BOM Day'          => $days($rndDrawingAt, $rndBomAt),
                
                    'Accounting Approved'  => $fmt($accountingAt),
                    'Accounting Day'       => $days($rndBomAt, $accountingAt),
                
                    'IT Approved'          => $fmt($itAt),
                    'IT Day'               => $days($accountingAt, $itAt),
                
                    'Released At'         => $fmt($transaction->released_at),

                    'Leading Time'         => $leadingTimeDay,
                
                    'Status'               => $transaction->status,
                    'As Additional Data'   => $transaction->as_additional_data ? 'Yes' : 'No',
                    'As Revision Data'     => $transaction->as_revision_data ? 'Yes' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Area',
            'SO Number',
            'PO Number',
            'Description',
            'Created At',
    
            'Upload Done At',
    
            'Sales Area Approved At',
            'Sales Area Day',
    
            'RnD Drawing Approved At',
            'RnD Drawing Day',
    
            'RnD BOM Approved At',
            'RnD BOM Day',
    
            'Accounting Approved At',
            'Accounting Day',
    
            'IT Approved At',
            'IT Day',
    
            'Released At',

            'Leading Time',
    
            'Status',
            'As Additional Data',
            'As Revision Data',
        ];
    }
}
