@extends('master.layout')

@section('content')
    <div>
        <table id="activityLogs" class="ui celled table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">User</th>
                    <th class="!font-bold">Action</th>
                    <th class="!font-bold">Module</th>
                    <th class="!font-bold">Description</th>
                    <th class="!font-bold">Date</th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        const activityLogsTable = $(document).ready(function() {
            $('#activityLogs').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('activityLogData') }}",
                columns: [
                    { data: 'user', name: 'user', orderable: false },
                    { data: 'action', name: 'action' },
                    { data: 'module', name: 'module' },
                    { data: 'description', name: 'description' },
                    { data: 'created_at', name: 'created_at' },
                ],
                columnDefs: [
                    { targets: 0, orderable: false },
                    { targets: 3, orderable: false },
                ],
                scrollX: true,
                fixedColumns: {
                    start: 0,
                    end: 0
                },
                layout: {
                    topStart: {
                        buttons: [
                            'pageLength',
                            'colvis'
                        ]
                    },
                    topEnd: {
                        search: 'applied'
                    }
                },
                order: [[4, 'desc']]
            });
        });        
    </script>
@endpush