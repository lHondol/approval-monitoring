<div class="ui dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    @php
        use App\Enums\StatusDrawingTransaction;
    @endphp
    <div class="menu">
        @if (auth()->user()->hasPermissionTo('view_drawing_transaction'))
            <a href="{{ route('drawingTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif
        
        @if ((auth()->user()->hasAnyPermission([
            'first_approve_drawing_transaction',
            'second_approve_drawing_transaction',
            'reject_drawing_transaction'    
        ])) && ($data->status === StatusDrawingTransaction::WAITING_1ST_APPROVAL->value || 
                $data->status === StatusDrawingTransaction::WAITING_2ND_APPROVAL->value
        ))
            <a href="{{ route('drawingTransactionApprovalForm', $data->id) }}" class="item">Approval</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('revise_drawing_transaction') &&
            $data->status === StatusDrawingTransaction::REVISE_NEEDED->value
        )
            <a href="{{ route('drawingTransactionReviseForm', $data->id) }}" class="item">Revise</a>
        @endif
    </div>
</div>