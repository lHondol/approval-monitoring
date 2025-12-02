@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'roleView', 'title' => 'Edit New Role'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('roleEdit', [$data->id]) }}" enctype="multipart/form-data">
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

                {{-- Role Name --}}
                <div class="field flex-1">
                    <label class="!text-base">Name</label>
                    <input type="text" name="name" placeholder="Name" value="{{ $data->name }}">
                </div>

                <div class="field flex-1 multiselect-wrapper">
                     <label class="!text-base">Permissions</label>
                    <div id="permissionsDropdown" class="ui clearable multiple selection dropdown">
                        <input type="hidden" name="permissions" value="{{ $data->permissions }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">Select Permissions</div>
                        <div class="menu">
                            @foreach ($permissions as $permission)
                                <div class="item" data-value="{{ $permission->id }}">{{ $permission->name }}</div>
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
            $('#permissionsDropdown').dropdown();
        });
    </script>
@endpush
