<div>
    <form class="ui form" method="post" action="{{ route('drawingTransactionCreate') }}" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="ui negative message">
                <div class="header">We had some issues</div>
                <ul class="list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach 
                </ul>
            </div>
        @endif

        <div class="field flex-1">
            <label class="!text-base"">Sales Order Number (SO)</label>
            <input type="text" name="so_number" placeholder="Sales Order Number"" disabled>
        </div>
        <div class="field">
            <label class="!text-base"">Reject Reason</label>
            <textarea style="resize: none;" name="reject_reason" placeholder="Reject Reason" disabled></textarea>
        </div>
    </form>
</div>
