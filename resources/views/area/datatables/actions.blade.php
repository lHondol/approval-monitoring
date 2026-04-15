<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_area'))
            <a href="{{ route('areaDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('edit_area'))
            <a href="{{ route('areaEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        <x-dropdown.delete
            :route="route('areaDelete', $data->id)"
            :id="$data->id"
            permission="delete_area"
            label="Remove"
        />
    </div>
</div>