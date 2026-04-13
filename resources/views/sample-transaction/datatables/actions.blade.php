<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_sample_transaction'))
            <a href="{{ route('sampleTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('create_sample_transaction_process'))
            <a href="{{ route('sampleTransactionCreateProcessForm', $data->id) }}" class="item">Create Process</a>
        @endif
    </div>
</div>