@extends('master.layout')

@section('content')
    <div>
        <table id="sampleTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>No</th>
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
                    <th class="!font-bold">No</th>
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
                order: [[11, 'desc']],
                columns: [
                    { data: 'no', name: 'no' },
                    { data: 'so_number', name: 'so_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'start_at', name: 'start_at' },
                    { data: 'shipment_request', name: 'shipment_request' },
                    { data: 'note', name: 'note' },
                    { data: 'latest_unfinished_process_name', name: 'latest_unfinished_process_name' },
                    { data: 'actual_process_days', name: 'actual_process_days' },
                    { data: 'total_lead_time', name: 'total_lead_time' },
                    { data: 'progress', name: 'progress', width: 50 },
                    { data: 'status', name: 'status', width: 120 },

                    { data: 'so_created_at', name: 'so_created_at', visible: false },
                ],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { targets: -1, visible:false, orderable: true, searchable: false } // Actions column
                ],
                scrollX: true,
                fixedColumns: {
                    start: 0,
                    end: 2
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