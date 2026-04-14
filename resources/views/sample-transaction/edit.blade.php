@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Edit Sample Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('sampleTransactionEdit', $data->id) }}" enctype="multipart/form-data">
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
                            {{ $data->so_number }}
                        </div>
                    </div>
                </div>

                <div class="field flex-1">
                     <label class="!text-base">Customer</label>
                    <div id="customersDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="customer" value="{{ $data->customer->id }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Customer</div>
                        <div class="menu">
                            @foreach ($customers as $customer)
                                <div class="item" data-value="{{ $customer->id }}">{{ $customer->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">SO Created At</label>
                    <div id="so-created-at" class="ui calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="so_created_at" placeholder="YYYY-MM-DD HH:MM AM" value="{{ $data->so_created_at }}">
                        </div>
                    </div>
                </div>

                
                <div class="field">
                    <label class="!text-base">Shipment Request</label>
                    <div id="shipment-request" class="ui calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="shipment_request" placeholder="YYYY-MM-DD HH:MM AM" value="{{ $data->shipment_request }}">
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="!text-base">Picture Received At</label>
                    <div id="picture-received-at" class="ui calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="picture_received_at" placeholder="YYYY-MM-DD HH:MM AM" value="{{ $data->picture_received_at }}">
                        </div>
                    </div>
                </div>

                <button class="ui button customButton mt-4" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#customersDropdown').dropdown();
        });


        $(document).ready(function() {
            const soCreatedAt = $('#so-created-at');
            const shipmentRequest = $('#shipment-request');
            const pictureReceivedAt = $('#picture-received-at');

            soCreatedAt.calendar({
                type: 'datetime'
            });

            shipmentRequest.calendar({
                type: 'datetime'
            });

            pictureReceivedAt.calendar({
                type: 'datetime'
            });
        });
    </script>
@endsection
