<div class="ui dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_user'))
            <a href="{{ route('userDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if (auth()->user()->hasPermissionTo('edit_user'))
            <a href="{{ route('userEditForm', $data->id) }}" class="item">Edit</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('delete_user'))
            <a href="{{ route('userDelete', $data->id) }}" class="item">Remove</a>
        @endif
    </div>
</div>