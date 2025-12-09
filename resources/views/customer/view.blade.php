@extends('master.layout')

@section('content')
    <div>
        <table id="customers" class="ui celled table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">Name</th>
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
            $('#customers').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('customerData') }}",
                columns: [
                    { data: 'name', name: 'name' },
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
                            @if(auth()->user()->hasPermissionTo('create_customer'))
                                {
                                    text: 'Add Record',
                                    className: 'customButton !ml-3',
                                    action: function () {
                                        window.location.href = "{{ route('customerCreateForm') }}";
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