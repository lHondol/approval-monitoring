<div class="ui dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        <a href="{{ route('roleDetail', $data->id) }}" class="item">Detail</a>
        <a href="{{ route('roleEditForm', $data->id) }}" class="item">Edit</a>
        <a href="{{ route('roleDelete', $data->id) }}" class="item">Remove</a>
    </div>
</div>