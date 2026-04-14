@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'sampleTransactionView', 'title' => 'Detail Sample Transaction', 'marginButtom' => '!mb-3'])

    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <div class="mb-4">
                <div class="font-bold mb-1">Sales Order Number (SO)</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->so_number }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Customer Name</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->customer->name }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">SO Created At</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->so_created_at }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Shipment Request</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->shipment_request }}
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-bold mb-1">Picture Received At</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->picture_received_at }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
