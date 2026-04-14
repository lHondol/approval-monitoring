@extends('master.layout')

@section('content')
    <div>
        <table id="sampleTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>So Number</th>
                    <th>Customer Name</th>
                    <th>So Created At</th>
                    <th>Shipment Request</th>
                    <th>Picture Received At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">So Number</th>
                    <th class="!font-bold">Customer Name</th>
                    <th class="!font-bold">So Created At</th>
                    <th class="!font-bold">Shipment Request</th>
                    <th class="!font-bold">Picture Received At</th>
                    <th class="!font-bold">Actions</th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        const customersTable = $(document).ready(function() {
            $('#sampleTransactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sampleTransactionData') }}",
                columns: [
                    { data: 'so_number', name: 'so_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'so_created_at', name: 'so_created_at' },
                    { data: 'shipment_request', name: 'shipment_request' },
                    { data: 'picture_received_at', name: 'picture_received_at' },
                    { data: 'actions', name: 'actions', width: 120 },
                ],
                columnDefs: [
                    { targets: -1, className: 'dt-center', orderable: false, searchable: false } // Actions column
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
                            @if(auth()->user()->hasPermissionTo('create_sample_transaction'))
                                {
                                    text: 'Add Record',
                                    className: 'customButton !ml-3',
                                    action: function () {
                                        window.location.href = "{{ route('sampleTransactionCreateForm') }}";
                                    }
                                }
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