<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_sample_transaction'))
            <a href="{{ route('sampleTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if (auth()->user()->hasPermissionTo('edit_sample_transaction'))
            <a href="{{ route('sampleTransactionEditForm', $data->id) }}" class="item">Edit</a>
        @endif

        @if (auth()->user()->hasPermissionTo('approve_sample_transaction') && empty($data->picture_received_at))
            <a href="{{ route('sampleTransactionApproveForm', $data->id) }}" class="item">Approve</a>
        @endif

        <x-dropdown.delete
            :route="route('sampleTransactionDelete', $data->id)"
            :id="$data->id"
            permission="delete_sample_transaction"
            label="Remove"
        />
        
        @if (auth()->user()->hasPermissionTo('create_sample_transaction_process') && !empty($data->picture_received_at) && is_null($data->latestUnfinishedProcess))
            <a href="{{ route('sampleTransactionCreateProcess', $data->id) }}" class="item">Start Process</a>
        @endif
    </div>
</div>