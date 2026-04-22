@extends('master.layout')

@section('content')
    <div>
        <table id="prereleaseSoTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SO Number</th>
                    <th>Buyer</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
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
            $('#prereleaseSoTransactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('prereleaseSoTransactionDashboardData') }}",
                columns: [
                    { data: 'no', name: 'no' },
                    { data: 'so_number', name: 'so_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'target_shipment', name: 'target_shipment' },
                    { data: 'current_process', name: 'current_process' },
                    { data: 'actual_process_days', name: 'actual_process_days' },
                    { data: 'total_lead_time', name: 'total_lead_time' },
                    { data: 'progress', name: 'progress', width: 50 },
                    { data: 'status', name: 'status', width: 120 },
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
                    { targets: -1, className: 'dt-center', orderable: false, searchable: false } // Actions column
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
                        buttons: [
                            @if(auth()->user()->hasPermissionTo('create_sample_transaction'))
                                {
                                    text: 'Add Record',
                                    className: 'customButton !ml-3',
                                    action: function () {
                                        window.location.href = "{{ route('prereleaseSoTransactionCreateForm') }}";
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