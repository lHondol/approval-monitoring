@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'areaView', 'title' => 'Detail Area', 'marginButtom' => '!mb-3'])

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

            <div class="mb-4">
                <div class="font-bold mb-1">Users</div>
                <div class="ui input w-full !cursor-default">
                    <div class="w-full px-5 py-5 !flex gap-3 flex-wrap rounded bg-gray-100 !text-black">
                        @if ($data->users)
                            @foreach ($data->users as $user)
                                <span class='ui teal label'>{{ $user }}</span>
                            @endforeach
                        @else
                            -- No User Yet --
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
