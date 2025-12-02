<div class="ui dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        <a href="{{ route('userDetail', $data->id) }}" class="item">Detail</a>
        <a href="{{ route('userEditForm', $data->id) }}" class="item">Edit</a>
        <a href="{{ route('userDelete', $data->id) }}" class="item">Remove</a>
    </div>
</div>