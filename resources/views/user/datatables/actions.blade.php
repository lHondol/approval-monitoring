<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_user'))
            <a href="{{ route('userDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if (auth()->user()->hasPermissionTo('edit_user'))
            <a href="{{ route('userEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        <x-dropdown.delete
            :route="route('userDelete', $data->id)"
            :id="$data->id"
            permission="delete_user"
            label="Remove"
        />
    </div>
</div>