<?php

namespace App\Exports;

use App\Models\SampleTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SampleTransactionsExport implements FromCollection, WithHeadings, WithEvents
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

        return SampleTransaction::with(['customer', 'processes'])
            ->when($from, fn ($q) => $q->whereDate('so_created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('so_created_at', '<=', $to))
            ->orderByDesc('so_created_at')
            ->get()
            ->values()
            ->flatMap(function ($transaction, $index) {

                $rows = [];

                foreach ($transaction->processes as $i => $p) {

                    $start = $p->start_at ? Carbon::parse($p->start_at) : null;
                    $finish = $p->finish_at ? Carbon::parse($p->finish_at) : null;

                    $days = ($start && $finish)
                    ? $start->copy()->startOfDay()->diffInDays(
                        $finish->copy()->startOfDay()
                      )
                    : 0;

                    $rows[] = [
                        'No' => $i === 0 ? $index + 1 : '',
                        'Nomor SO' => $i === 0 ? $transaction->so_number : '',
                        'Pembeli' => $i === 0 ? ($transaction->customer->name ?? '') : '',
                        'SO Dibuat' => $i === 0 ? (Carbon::parse($transaction->so_created_at)->format('Y-m-d H:i') ?? '') : '',
                        'Shipment Request' => $i === 0 ? (Carbon::parse($transaction->shipment_request)->format('Y-m-d H:i') ?? '') : '',
                        'Terima Gambar' => $i === 0 ? (Carbon::parse($transaction->picture_received_at)->format('Y-m-d H:i') ?? '') : '',
                        'Proses' => $p->process_name,
                        'Start' => $start ? $start->format('Y-m-d H:i') : '',
                        'Finish' => $finish ? $finish->format('Y-m-d H:i') : '',
                        'Total Hari' => (string)$days,
                    ];
                }

                if (count($rows) === 0) {
                    $rows[] = [
                        'No' => $index + 1,
                        'Nomor SO' => $transaction->so_number,
                        'Pembeli' => $transaction->customer->name ?? '',
                        'SO Dibuat' => Carbon::parse($transaction->so_created_at)->format('Y-m-d H:i') ?? '',
                        'Shipment Request' => Carbon::parse($transaction->shipment_request)->format('Y-m-d H:i') ?? '',
                        'Terima Gambar' => Carbon::parse($transaction->picture_received_at)->format('Y-m-d H:i') ?? '',
                        'Proses' => '',
                        'Start' => '',
                        'Finish' => '',
                        'Total Hari' => '',
                    ];
                }

                return $rows;
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor SO',
            'Pembeli',
            'SO Dibuat',
            'Shipment Request',
            'Terima Gambar',
            'Proses',
            'Start',
            'Finish',
            'Total Hari',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
    
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
    
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
    
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
    
                $row = 2;
    
                while ($row <= $highestRow) {
    
                    $startRow = $row;
                    $row++;
    
                    while (
                        $row <= $highestRow &&
                        empty($sheet->getCell("A{$row}")->getValue())
                    ) {
                        $row++;
                    }
    
                    $endRow = $row - 1;
    
                    if ($endRow > $startRow) {
                        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                            $sheet->mergeCells("{$col}{$startRow}:{$col}{$endRow}");
                        }
    
                        $sheet->getStyle("A{$startRow}:C{$endRow}")
                            ->getAlignment()
                            ->setVertical(Alignment::VERTICAL_CENTER)
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            },
        ];
    }
}
