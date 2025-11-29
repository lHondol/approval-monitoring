<div>
    <form class="ui form" method="post" action="{{ route('drawingTransactionApproval', $data->id) }}" enctype="multipart/form-data">
        @csrf

        @if ($errors->approval->any())
            <div class="ui negative message">
                <div class="header">We had some issues</div>
                <ul class="list">
                    @foreach ($errors->approval->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach 
                </ul>
            </div>
        @endif

        <div class="field flex-1">
            <label class="!text-base"">Sales Order Number (SO)</label>
            <input type="text" name="so_number" placeholder="Sales Order Number"">
        </div>
        <div class="field">
            <label class="!text-base"">Reject Reason (Must be fill if reject)</label>
            <textarea style="resize: none;" name="reject_reason" placeholder="Reject Reason"></textarea>
        </div>
        <div>
            <button class="ui button customButton" type="submit" name="action" value="approve">Approve</button>
            <button class="ui button customButton" style="--btn-color: #e74c3c;" type="submit" name="action" value="reject">Reject</button>
        </div>
    </form>
</div>
