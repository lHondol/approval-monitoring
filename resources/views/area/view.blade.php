@extends('master.layout')

@section('content')
    <div>
        <table id="areas" class="ui celled table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Users</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">Name</th>
                    <th class="!font-bold">Users</th>
                    <th class="!font-bold">Actions</th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        const areasTable = $(document).ready(function() {
            $('#areas').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('areaData') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'users', name: 'users' },
                    { data: 'actions', name: 'actions', width: 120 },
                ],
                columnDefs: [
                    { targets: 1, orderable: false },
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
                            @if(auth()->user()->hasPermissionTo('create_area'))
                                {
                                    text: 'Add Record',
                                    className: 'customButton !ml-3',
                                    action: function () {
                                        window.location.href = "{{ route('areaCreateForm') }}";
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