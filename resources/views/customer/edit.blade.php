@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'customerView', 'title' => 'Edit Customer'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('customerEdit', [$data->id]) }}" enctype="multipart/form-data">
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

                <div class="field flex-1">
                    <label class="!text-base">Name</label>
                    <input type="text" name="name" placeholder="Name" value="{{ $data->name }}">
                </div>

                <button class="ui button customButton mt-4" type="submit">Submit</button>
            </form>
        </div>
    </div>
@endsection