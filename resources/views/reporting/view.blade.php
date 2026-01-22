@extends('master.layout')

@section('content')
    @include('shared.appbar', ['title' => 'Reporting', 'hideBackButton' => true])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('reportingExport') }}">
                @csrf

                @if ($errors->any())
                    <div class="ui negative message">
                        <div class="header">We had some issues</div>
                        <ul class="list">
                        @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                        @endforeach 
                        </ul>
                    </div>
                @endif

                @php
                    $topics = [["token" => "drawing_transaction","name" => "Drawing Transaction"]]
                @endphp
                <div class="field flex-1">
                     <label class="!text-base">Topic</label>
                    <div id="topicsDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="topic">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Topic</div>
                        <div class="menu">
                            @foreach ($topics as $topic)
                                <div class="item" data-value="{{ $topic['token'] }}">{{ $topic['name'] }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="two fields">
                    <div class="field">
                        <label class="!text-base">From Date</label>
                        <div id="from-date" class="ui calendar">
                            <div class="ui input left icon">
                                <i class="calendar icon"></i>
                                <input type="text" name="from_date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="!text-base">To Date</label>
                        <div id="to-date" class="ui calendar">
                            <div class="ui input left icon">
                                <i class="calendar icon"></i>
                                <input type="text" name="to_date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                    </div>
                </div>

                <button class="ui button customButton mt-4" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#topicsDropdown').dropdown();
        });
        $(document).ready(function() {
            const from = $('#from-date');
            const to = $('#to-date');

            from.calendar({
                type: 'date',
                endCalendar: to
            });

            to.calendar({
                type: 'date',
                startCalendar: from
            });
        });
    </script>
@endpush
