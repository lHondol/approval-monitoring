@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'customerView', 'title' => 'Detail Customer', 'marginButtom' => '!mb-3'])

    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <div class="mb-4">
                <div class="font-bold mb-1">Name</div>
                <div class="ui input w-full !cursor-default opacity-70">
                    <div class="w-full px-5 py-2 rounded bg-gray-100 !text-black">
                        {{ $data->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
