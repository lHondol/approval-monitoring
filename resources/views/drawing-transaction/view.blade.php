@extends('master.layout')

@section('content')
    <div>
        <table id="drawingTransactions" class="ui celled table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>SO Number</th>
                <th>PO Number</th>
                <th>Created At</th>
                <th>Distributed At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th class="!font-bold">Customer Name</th>
                <th class="!font-bold">SO Number</th>
                <th class="!font-bold">PO Number</th>
                <th class="!font-bold">Created At</th>
                <th class="!font-bold">Distributed At</th>
                <th class="!font-bold">Status</th>
                <th class="!font-bold">Actions</th>
            </tr>
        </tfoot>
    </table>
    </div>
@endsection

@push('scripts')
    <script>
        const drawingTransactionsTable = $(document).ready(function() {
            $('#drawingTransactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('drawingTransactionData') }}",
                columns: [
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'so_number', name: 'so_number' },
                    { data: 'po_number', name: 'po_number' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'distributed_at', name: 'distributed_at' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions' },
                ],
                columnDefs: [
                    { targets: 1, className: 'dt-left' }, // force left alignment for SO Number (detected as number)
                    { targets: -1, width: '10%', className: 'dt-center', orderable: false, searchable: false } // Actions column
                ],
                scrollX: true,
                fixedColumns: {
                    start: 0,
                    end: 1
                },
                layout: {
                    topStart: {
                        buttons: [
                            'pageLength', 
                            'colvis'
                        ]
                    },
                    topEnd: {
                        search: 'applied',
                        buttons: [
                            {
                                text: 'Add Record',
                                className: 'customButton !ml-3',
                                action: function () {
                                    window.location.href = "{{ route('drawingTransactionCreateForm') }}";
                                }
                            }
                        ]
                    }
                },
                initComplete: function() {
                    $(".ui.dropdown").dropdown({
                        direction: 'auto'
                    });
                    const fixedTds = document.querySelectorAll('td.dt-center.dtfc-fixed-end.dtfc-fixed-right');
                    fixedTds.forEach((td, index) => {
                        td.style.zIndex = 99999 - index;   // 100, 101, 102, ...
                        console.log(td, td.style.zIndex);
                    });
                }
            });
        });        
    </script>
@endpush