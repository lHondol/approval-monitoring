@extends('master.layout')

@section('content')
    @include('shared.appbar', ['backRoute' => 'prereleaseSoTransactionView', 'title' => 'Approval Prerelease So Transaction'])
    <div class="flex justify-center">
        <div class="ui card !w-[800px] !p-8">
            <form class="ui form" method="post" action="{{ route('prereleaseSoTransactionApproval', $data->id) }}" enctype="multipart/form-data">
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
                
                <div class="field">
                    <label class="!text-base"">Reason (Must be fill if reject)</label>
                    <textarea style="resize: none;" name="reason" placeholder="Reason"></textarea>
                </div>
                <div>
                    @if (auth()->user()->hasAnyPermission([
                        'sales_area_approve_prerelease_so_transaction',
                        'rnd_drawing_approve_prerelease_so_transaction',
                        'rnd_bom_approve_prerelease_so_transaction',
                        'accounting_approve_prerelease_so_transaction',
                        'it_approve_prerelease_so_transaction',
                    ]))
                        <button class="ui button customButton" type="submit" name="action" value="approve">Approve</button>
                    @endif

                    @if (auth()->user()->hasPermissionTo('reject_prerelease_so_transaction'))
                        <button class="ui button customButton" style="--btn-color: #e74c3c;" type="submit" name="action" value="reject">Reject</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection