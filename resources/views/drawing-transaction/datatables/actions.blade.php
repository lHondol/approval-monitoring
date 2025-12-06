<div class="ui dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    @php
        use App\Enums\StatusDrawingTransaction;
    @endphp
    <div class="menu">
        @php
            $user = auth()->user();
            $status = $data->status;

            $canFirstApprove =
                $user->hasPermissionTo('first_approve_drawing_transaction') &&
                $status === StatusDrawingTransaction::WAITING_1ST_APPROVAL->value;

            $canSecondApprove =
                $user->hasPermissionTo('second_approve_drawing_transaction') &&
                $status === StatusDrawingTransaction::WAITING_2ND_APPROVAL->value;

            $canReject =
                ($user->hasPermissionTo('reject_drawing_transaction') && $canFirstApprove) ||
                ($user->hasPermissionTo('reject_drawing_transaction') && $canSecondApprove);
        @endphp

        @if (auth()->user()->hasPermissionTo('view_drawing_transaction'))
            <a href="{{ route('drawingTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if ($canFirstApprove || $canSecondApprove || $canReject)
            <a href="{{ route('drawingTransactionApprovalForm', $data->id) }}" class="item">Approval</a>
        @endif
        
        @if (auth()->user()->hasPermissionTo('revise_drawing_transaction') &&
            $data->status === StatusDrawingTransaction::REVISE_NEEDED->value
        )
            <a href="{{ route('drawingTransactionReviseForm', $data->id) }}" class="item">Revise</a>
        @endif
    </div>
</div>