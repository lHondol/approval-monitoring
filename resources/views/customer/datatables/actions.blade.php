<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_customer'))
            <a href="{{ route('customerDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('edit_customer'))
            <a href="{{ route('customerEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        @if (auth()->user()->hasPermissionTo('delete_customer'))
            <a href="{{ route('customerDelete', $data->id) }}" class="item">Remove</a>
        @endif
    </div>
</div>