@extends('master.layout')

@section('content')
    <div>
        <table id="sampleTransactions" class="ui celled table">
            <thead>
                <tr>
                    <th>Processes</th>
                    <th>So Number</th>
                    <th>Customer Name</th>
                    <th>So Created At</th>
                    <th>Shipment Request</th>
                    <th>Drawing Received At</th>
                    <th>Note</th>
                    <th>Approve Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th class="!font-bold">Processes</th>
                    <th class="!font-bold">So Number</th>
                    <th class="!font-bold">Customer Name</th>
                    <th class="!font-bold">So Created At</th>
                    <th class="!font-bold">Shipment Request</th>
                    <th class="!font-bold">Drawing Received At</th>
                    <th class="!font-bold">Approve Note</th>
                    <th class="!font-bold">Note</th>
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
            $('#sampleTransactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sampleTransactionData') }}",
                columns: [
                    {
                        targets: 0,
                        className: 'dt-control',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '',
                    },
                    { data: 'so_number', name: 'so_number' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'so_created_at', name: 'so_created_at' },
                    { data: 'shipment_request', name: 'shipment_request' },
                    { data: 'picture_received_at', name: 'picture_received_at' },
                    { data: 'note', name: 'note' },
                    { data: 'picture_received_note', name: 'picture_received_note' },
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
        
        $('#sampleTransactions tbody').on('click', 'td.dt-control', function () {
            let tr = $(this).closest('tr');
            let row = $('#sampleTransactions').DataTable().row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show();
                tr.addClass('shown');

                setTimeout(() => {
                    initDropdownPortal();
                }, 0);
            }
        });

        const actionTemplate = @json(
            view('js-templates.sample-transaction-process.actions')->render()
        );

        function format(data) {
            let processes = data.processes;

            if (typeof processes === 'string') {
                processes = JSON.parse(processes);
            }

            let html = `<table class="ui celled table" style="width:100%; margin:10px 0;">
                <thead>
                    <tr style="background-color:#f9fafb !important;">
                        <th style="padding:13px 11px !important;">Process Name</th>
                        <th style="padding:13px 11px !important;">Start At</th>
                        <th style="padding:13px 11px !important;">Finish At</th>
                        <th style="padding:13px 11px !important;">Total Day</th>
                        <th style="text-align:center;">Picture</th>
                        <th style="padding:13px 11px !important;">Start Note</th>
                        <th style="padding:13px 11px !important;">Finish Note</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>`;

            const routeTemplate = {
                edit: "{{ route('sampleTransactionEditProcessForm', ':id') }}",
                delete: "{{ route('sampleTransactionDeleteProcess', ':id') }}"
            };

            if (processes && processes.length > 0) {
                processes.forEach(p => {
                    
                    let actions = actionTemplate.replaceAll('__ID__', p.id);

                    let temp = document.createElement('div');
                    temp.innerHTML = actions;

                    if (p.finish_at) {
                        const finishBtn = temp.querySelector('.btn-finish');
                        if (finishBtn) finishBtn.remove();
                    }

                    actions = temp.innerHTML;

                    html += `
                        <tr>
                            <td>${p.process_name}</td>
                            <td>${p.start_at ?? ''}</td>
                            <td>${p.finish_at ?? ''}</td>
                            <td>${p.total_day ?? ''}</td>
                            <td style="text-align:center;">
                                ${
                                    p.file_url 
                                    ? `<a href="${p.file_url}" target="_blank" class="ui icon">
                                        <i class="file alternate large icon"></i>
                                    </a>`
                                    : ''
                                }
                            </td>
                            <td>${p.start_note ?? ''}</td>
                            <td>${p.finish_note ?? ''}</td>
                            <td style="text-align:center;">
                                ${actions}
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr><td colspan="4" class="text-center">No Data</td></tr>`;
            }

            html += `</tbody></table>`;
            return html;
        }
    </script>
@endpush