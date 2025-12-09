<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_role'))
            <a href="{{ route('roleDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('edit_role'))
            <a href="{{ route('roleEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        @if (auth()->user()->hasPermissionTo('delete_role'))
            <a href="{{ route('roleDelete', $data->id) }}" class="item">Remove</a>
        @endif
    </div>
</div>