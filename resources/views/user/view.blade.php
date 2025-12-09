@extends('master.layout')

@section('content')
<div>
    <table id="users" class="ui celled table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th class="!font-bold">Name</th>
                <th class="!font-bold">Email</th>
                <th class="!font-bold">Role</th>
                <th class="!font-bold">Actions</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function () {

            const table = $('#users').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('userData') }}",
                columns: [
                    { data: 'name', name: 'name', },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'actions', name: 'actions', width: 120 },
                ],
                scrollX: true,
                fixedColumns: {
                    start: 0,
                    end: 1
                },
                columnDefs: [
                    {
                        targets: -1,
                        className: 'dt-center',
                        orderable: false,
                        searchable: false
                    }
                ],
                layout: {
                    topStart: { buttons: ['pageLength', 'colvis'] },
                    topEnd: { search: 'applied' },
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
