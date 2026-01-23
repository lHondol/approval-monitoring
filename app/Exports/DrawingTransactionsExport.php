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

        return DrawingTransaction::with(['customer', 'steps'])
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

                $approve1At = $nextStep(
                    $uploadAt,
                    optional($steps['Approve - 1'] ?? null)?->done_at
                );

                $approve2At = $nextStep(
                    $approve1At,
                    optional($steps['Approve - 2'] ?? null)?->done_at
                );

                $approveBomAt = $nextStep(
                    $transaction->distributed_at,
                    optional($steps['Approve - BOM'] ?? null)?->done_at
                );

                $approveCostAt = $nextStep(
                    $approveBomAt,
                    optional($steps['Approve - Costing'] ?? null)?->done_at
                );

                return [
                    'Customer Name'       => $transaction->customer->name ?? '',
                    'SO Number'           => $transaction->so_number ?? '',
                    'PO Number'           => $transaction->po_number,
                    'Description'         => $transaction->description,
                    'Created At'          => $fmt($transaction->created_at),

                    'Upload Done At'      => $fmt($uploadAt),
                    'Approve 1 Done At'   => $fmt($approve1At),
                    'Approval 1 Day'      => $days($uploadAt, $approve1At),

                    'Approve 2 Done At'   => $fmt($approve2At),
                    'Approval 2 Day'      => $days($approve1At, $approve2At),

                    'Distributed At'      => $fmt($transaction->distributed_at),

                    'Approve BOM Done At' => $fmt($approveBomAt),
                    'Approval BOM Day'    => $days(
                                                $transaction->distributed_at,
                                                $approveBomAt
                                            ),

                    'Approve Costing Done At' => $fmt($approveCostAt),
                    'Approval Costing Day'   => $days(
                                                    $approveBomAt,
                                                    $approveCostAt
                                                ),

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
            'Approval 1 Day',
            'Approve 2 Done At',
            'Approval 2 Day',
            'Distributed At',
            'Approve BOM Done At',
            'Approval BOM Day',
            'Approve Costing Done At',
            'Approval Costing Day',

            'Status',
            'As Additional Data',
            'As Revision Data',
        ];
    }
}
