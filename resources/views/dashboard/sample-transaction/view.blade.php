@extends('master.layout')

@section('content')
    <div>
        <table id="sampleTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>SO Number</th>
                    <th>Buyer</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                    <th>Product</th>
                    <th>Current Process</th>
                    <th>Actual Process Days</th>
                    <th>Total Lead Time</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">SO Number</th>
                    <th class="!font-bold">Buyer</th>
                    <th class="!font-bold">Start Date</th>
                    <th class="!font-bold">Due Date</th>
                    <th class="!font-bold">Product</th>
                    <th class="!font-bold">Current Process</th>
                    <th class="!font-bold">Actual Process Days</th>
                    <th class="!font-bold">Total Lead Time</th>
                    <th class="!font-bold">Progress</th>
                    <th class="!font-bold">Status</th>
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
                ajax: "{{ route('sampleTransactionDashboardData') }}",
                columns: [
                    { data: 'so_number', name: 'so_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'start_at', name: 'start_at' },
                    { data: 'shipment_request', name: 'shipment_request' },
                    { data: 'note', name: 'note' },
                    { data: 'latest_unfinished_process_name', name: 'latest_unfinished_process_name' },
                    { data: 'actual_process_days', name: 'actual_process_days' },
                    { data: 'total_lead_time', name: 'total_lead_time' },
                    { data: 'progress', name: 'progress' },
                    { data: 'status', name: 'status' },
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
                            {
                                extend: 'colvis',
                                columns: ':gt(0)'
                            }
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