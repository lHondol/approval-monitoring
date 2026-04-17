@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Start New Sample Transaction Process'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionCreateProcess', $sampleTransaction->id) }}" enctype="multipart/form-data">
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
                <div class="mb-4">
                    <div class="font-bold mb-1">Sales Order Number (SO)</div>
                    <div class="ui input w-full !cursor-default opacity-70">
                        <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                            {{ $sampleTransaction->so_number }}
                        </div>
                    </div>
                </div>

                <div class="field flex-1">
                     <label class="!text-base">Process</label>
                    <div id="processesDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="process">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Process</div>
                        <div class="menu">
                            @foreach ($processes as $process)
                                <div class="item" data-value="{{ $process }}">{{ $process }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Start Note</label>
                    <textarea style="resize: none;" name="start_note" placeholder="Start Note" rows="3"></textarea>
                </div>

                <button class="ui button customButton mt-4" type="submit">Start</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#processesDropdown').dropdown();
        });
    </script>
@endsection
