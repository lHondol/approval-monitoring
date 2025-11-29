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
                <th>Customer Name</th>
                <th>SO Number</th>
                <th>PO Number</th>
                <th>Created At</th>
                <th>Distributed At</th>
                <th>Status</th>
                <th>Actions</th>
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
                    { targets: 1, className: 'dt-left' } // force left alignment for SO Number (detected as number)
                ],
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
            });
        });

        $('#drawingTransactions').on('init.dt', function() {
    $('.dt-length select').addClass('ui dropdown');
});
    </script>
@endpush