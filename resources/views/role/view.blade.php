@extends('master.layout')

@section('content')
    <div>
        <table id="roles" class="ui celled table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th class="!font-bold">Name</th>
                <th class="!font-bold">Permissions</th>
                <th class="!font-bold">Actions</th>
            </tr>
        </tfoot>
    </table>
    </div>
@endsection

@push('scripts')
    <script>
        const rolesTable = $(document).ready(function() {
            $('#roles').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('roleData') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'permissions', name: 'permissions' },
                    { data: 'actions', name: 'actions' },
                ],
                columnDefs: [
                    { targets: 1, width: '15%', className: 'dt-left max-w-[800px]' }, // force left alignment for SO Number (detected as number)
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
                                    window.location.href = "{{ route('roleCreateForm') }}";
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