@extends('master.layout')

@section('content')
<style>
    .fc-event {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
    }

    .fc-event-main {
        padding: 0 !important;
        color: black !important;
        font-weight: bold !important;
        font-size: small;
    }

    .fc-daygrid-event {
        margin: 0 !important;
    }
</style>
@include('shared.appbar', ['title' => 'Sample Transaction Calendar', 'hideBackButton' => true])
    <div class="ui card !w-full !p-6">
        <div id="calendar"></div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 650,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                eventContent: function(arg) {
                    return {
                        html: `
                            <div>
                                <span style="color:red;">${arg.event.extendedProps.customer_name}</span>
                                <span> - ${arg.event.extendedProps.so_number}</span>
                            </div>
                        `
                    };
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch("{{ route('sampleTransactionCalendarData') }}?start=" 
                        + fetchInfo.startStr + "&end=" + fetchInfo.endStr)
                        .then(response => response.json())
                        .then(data => {
                            const mapped = data.map(e => ({
                                id: e.id,
                                start: e.start,
                                title: '', // required but unused
                                extendedProps: {
                                    customer_name: e.customer_name,
                                    so_number: e.so_number
                                }
                            }));
                            successCallback(mapped);
                        })
                        .catch(() => failureCallback());
                },
                eventClick: function(info) {
                    const id = info.event.id;
                    window.location.href = `/sample-transactions/detail/${id}`;
                }
            });

            calendar.render();
        });
    </script>
@endpush