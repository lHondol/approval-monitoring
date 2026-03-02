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
                @php
                    use App\Enums\StatusPrereleaseSoTransaction;
                    $canRelease = 
                        auth()->user()->hasPermissionTo('mkt_staff_release_prerelease_so_transaction') &&
                        $data->status === StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_RELEASE->value;

                    $canAccountingRequestConfirmMargin =
                        auth()->user()->hasPermissionTo('accounting_request_confirm_margin_prerelease_so_transaction') &&
                        $data->status === StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value &&
                        $data->is_margin_confirmed !== 1;

                    $canMKTManagerConfirmMargin =
                        auth()->user()->hasPermissionTo('mkt_manager_confirm_margin_prerelease_so_transaction') &&
                        $data->status === StatusPrereleaseSoTransaction::WAITING_MKT_MGR_CONFIRM_MARGIN->value;

                    $released = $data->status === StatusPrereleaseSoTransaction::RELEASED_PO_KACA_DONE->value;

                    $needRevised = $data->status === StatusPrereleaseSoTransaction::REVISE_NEEDED->value;
                    
                @endphp
                <div class="field">
                    <label class="!text-base">
                        @if ($canRelease || !auth()->user()->hasPermissionTo('mkt_admin_reject_prerelease_so_transaction'))
                            Reason
                        @else
                            Reason (Must be fill if reject)
                        @endif
                    </label>
                    <textarea style="resize: none;" name="reason" placeholder="Reason"></textarea>
                </div>
                <div>
                    @if ((auth()->user()->hasAnyPermission([
                        'sales_area_approve_prerelease_so_transaction',
                        'rnd_drawing_approve_prerelease_so_transaction',
                        'rnd_bom_approve_prerelease_so_transaction',
                        'accounting_approve_prerelease_so_transaction',
                        'accounting_request_confirm_margin_prerelease_so_transaction',
                        'it_approve_prerelease_so_transaction',
                        'po_kaca_released_approve_prerelease_so_transaction'
                    ]) || $canRelease || $canMKTManagerConfirmMargin) && !$released)
                        <button class="ui button customButton" type="submit" name="action" value="approve">
                            @if ($canRelease)
                                Release
                            @elseif ($canMKTManagerConfirmMargin)
                                Confirm Margin    
                            @else
                                Approve
                            @endif
                        </button>
                    @endif

                    @if ($canAccountingRequestConfirmMargin)
                        <button class="ui button customButton" type="submit" name="action" value="request-confirm-margin">Request Confirm Margin</button>
                    @endif

                    @if (auth()->user()->hasAnyPermission(['reject_prerelease_so_transaction', 'mkt_admin_reject_prerelease_so_transaction']) && !$canMKTManagerConfirmMargin && !$needRevised)
                        <button class="ui button customButton" style="--btn-color: #e74c3c;" type="submit" name="action" value="reject">Reject</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection