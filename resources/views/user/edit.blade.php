@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'userView', 'title' => 'Edit User'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('userEdit', [$data->id]) }}" enctype="multipart/form-data">
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

                <div class="field flex-1">
                    <label class="!text-base">Email</label>
                    <input type="text" name="email" placeholder="Email" value="{{ $data->email }}">
                </div>

                <div class="field flex-1">
                     <label class="!text-base">Role</label>
                    <div id="rolesDropdown" class="ui clearable selection dropdown">
                        <input type="hidden" name="role" value="{{ $data->role }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Role</div>
                        <div class="menu">
                            @foreach ($roles as $role)
                                <div class="item" data-value="{{ $role->id }}">{{ $role->name }}</div>
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
        $(document).ready(function() {
            $('#rolesDropdown').dropdown();
        });
    </script>
@endpush
