@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'drawingTransactionView', 'title' => 'Approval Drawing Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('drawingTransactionApproval', $data->id) }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="active_tab" id="active_tab_global">
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
                

                @php
                    use App\Enums\StatusDrawingTransaction;
                @endphp
                @if ($data->status === StatusDrawingTransaction::WAITING_2ND_APPROVAL->value && !$data->so_number)
                    <div class="field flex-1">
                        <label class="!text-base"">Sales Order Number (SO)</label>
                        <input type="text" name="so_number" placeholder="Sales Order Number"">
                    </div>
                @endif
                <div class="field">
                    <label class="!text-base"">Reason (Must be fill if reject)</label>
                    <textarea style="resize: none;" name="reason" placeholder="Reason"></textarea>
                </div>
                <div>
                    <button class="ui button customButton" type="submit" name="action" value="approve">Approve</button>
                    <button class="ui button customButton" style="--btn-color: #e74c3c;" type="submit" name="action" value="reject">Reject</button>
                </div>
            </form>
        </div>
    </div>
@endsection