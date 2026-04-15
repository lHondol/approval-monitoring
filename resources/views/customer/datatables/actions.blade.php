<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_customer'))
            <a href="{{ route('customerDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('edit_customer'))
            <a href="{{ route('customerEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        <x-dropdown.delete
            :route="route('customerDelete', $data->id)"
            :id="$data->id"
            permission="delete_customer"
            label="Remove"
        />
    </div>
</div>