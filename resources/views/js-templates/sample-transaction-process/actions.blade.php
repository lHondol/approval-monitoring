<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>

    <div class="menu">
        @if (auth()->user()->hasPermissionTo('edit_sample_transaction_process'))
            <a href="{{ route('sampleTransactionEditProcessForm', '__ID__') }}" class="item">
                Edit
            </a>
        @endif

        <x-dropdown.delete
            :route="route('sampleTransactionDeleteProcess', '__ID__')"
            :id="'__ID__'"
            permission="delete_sample_transaction_process"
            label="Remove"
        />
    </div>
</div>