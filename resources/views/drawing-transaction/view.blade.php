@extends('master.layout')

@section('content')
    <div>
        <table id="drawingTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>SO Number</th>
                    <th>PO Number</th>
                    <th>Description</th>
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
                    <th class="!font-bold">Description</th>
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
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#drawingTransactions').DataTable({
                processing: true,
                serverSide: true,
                order: [[4, 'desc']],
                ajax: "{{ route('drawingTransactionData') }}",
                columns: [
                    { data: 'customer_name', name: 'customer_name', width: 150, orderable: true },
                    { data: 'so_number', name: 'so_number', width: 150 },
                    { data: 'po_number', name: 'po_number', width: 150 },
                    { data: 'description', name: 'description', width: 200 },
                    { data: 'created_at', name: 'created_at', width: 130 },
                    { data: 'distributed_at', name: 'distributed_at', width: 130 },
                    { data: 'status', name: 'status', width: 200 },
                    { data: 'actions', name: 'actions', width: 120 },
                ],
                columnDefs: [
                    { targets: 1, className: 'dt-left' }, // force left alignment for SO Number (detected as number)
                    {
                        targets: 6,
                        className: 'dt-left',
                    },
                    {
                        targets: -1,
                        className: 'dt-center',
                        orderable: false,
                        searchable: false
                    }
                ],
                scrollX: true,
                fixedColumns: { start: 1, end: 2 },
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
                            @if(auth()->user()->hasPermissionTo('create_drawing_transaction'))
                                {
                                    text: 'Add Record',
                                    className: 'customButton !ml-3',
                                    action: function () {
                                        window.location.href = "{{ route('drawingTransactionCreateForm') }}";
                                    }
                                },
                            @endif
                        ]
                    }
                },
                initComplete: function () {
                    initDropdownPortal();
                },
                drawCallback: function () {
                    initDropdownPortal();
                }
            });
        });        
    </script>
@endpush