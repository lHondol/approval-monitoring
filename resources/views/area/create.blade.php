@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'areaView', 'title' => 'Create New Area'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('areaCreate') }}" enctype="multipart/form-data">
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
                    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}">
                </div>

                <div class="field flex-1 multiselect-wrapper">
                     <label class="!text-base">Users</label>
                    <div id="usersDropdown" class="ui clearable multiple selection dropdown">
                        <input type="hidden" name="users">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Users</div>
                        <div class="menu">
                            @foreach ($users as $user)
                                <div class="item" data-value="{{ $user->id }}">{{ $user->name }}</div>
                            @endforeach
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
        $('#usersDropdown').dropdown();
    </script>
@endpush
