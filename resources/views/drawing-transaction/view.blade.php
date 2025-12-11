@extends('master.layout')

@section('content')
    <div>
        <div class="flex flex-col gap-3 w-fit">
            <div id="statusFilterDt" 
                class="ui clearable selection dropdown" 
                style="min-width: 180px;">
                <input type="hidden" name="status">
                <i class="dropdown icon"></i>
                <div class="default text">Filter Status</div>
                <div class="menu">
                    <div class="item" data-value="Waiting">Waiting For Approval</div>
                    <div class="item" data-value="Waiting For 1st Approval">Waiting For Approval 1</div>
                    <div class="item" data-value="Waiting For 2nd Approval">Waiting For Approval 2</div>
                    <div class="item" data-value="Distributed">Distributed</div>
                </div>
            </div>
            <div class="flex gap-3" id="additionalRevisedFilter">
                <div class="ui checkbox" id="checkboxRevisionData">
                    <input type="checkbox" tabindex="0" class="hidden" name="as_revision_data">
                    <label>As Revision Data</label>
                </div>
                <div class="ui checkbox" id="checkboxAdditionalData">
                    <input type="checkbox" tabindex="0" class="hidden" name="as_additional_data">
                    <label>As Additional Data</label>
                </div>
                <div class="ui checkbox" id="checkboxDoneRevised">
                    <input type="checkbox" tabindex="0" class="hidden" name="done_revised">
                    <label>Revised</label>
                </div>
            </div>
        </div>
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
        var drawingTransactionTable = undefined
        $(document).ready(function() {
            drawingTransactionTable = $('#drawingTransactions').DataTable({
                processing: true,
                serverSide: true,
                order: [[4, 'desc']],
                ajax: {
                    url: "{{ route('drawingTransactionData') }}",
                    data: function(d) {
                        d.revision = $('#checkboxRevisionData').checkbox('is checked') ? '1' : '';
                        d.additional = $('#checkboxAdditionalData').checkbox('is checked') ? '1' : '';
                        d.revised = $('#checkboxDoneRevised').checkbox('is checked') ? '1' : '';
                    }
                },  
                columns: [
                    { data: 'customer_name', name: 'customer_name', width: 150, orderable: true },
                    { data: 'so_number', name: 'so_number', width: 150 },
                    { data: 'po_number', name: 'po_number', width: 150 },
                    { data: 'description', name: 'description', width: 200 },
                    { data: 'created_at', name: 'created_at', width: 130 },
                    { data: 'distributed_at', name: 'distributed_at', width: 130 },
                    { data: 'status', name: 'status', width: 200 },
                    { data: 'as_revision_data', name: 'as_revision_data', visible: false },
                    { data: 'as_additional_data', name: 'as_additional_data', visible: false },
                    { data: 'done_revised', name: 'done_revised', visible: false },
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
        
        $('#statusFilterDt').dropdown({
            on: 'click',
            onChange: function(value) {
                drawingTransactionTable
                .column('status:name')
                .search(value)
                .draw();
            }
        });

        $('#checkboxRevisionData, #checkboxAdditionalData, #checkboxDoneRevised').checkbox({
            onChange: function() {
                drawingTransactionTable.draw(); // triggers ajax.data and sends checkbox values
            }
        });
    </script>
@endpush