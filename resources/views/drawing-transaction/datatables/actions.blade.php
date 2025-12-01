<div class="ui dropdown icon button !px-5">
    Actions <i class="dropdown icon"></i>
    <div class="menu">
        <a href="{{ route('drawingTransactionDetailForm', $data->id) }}" class="item">Detail</a>
        <a href="{{ route('drawingTransactionApprovalForm', $data->id) }}" class="item">Approval</a>
        <a href="{{ route('drawingTransactionReviseForm', $data->id) }}" class="item">Revise</a>
    </div>
</div>