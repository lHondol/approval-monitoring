<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    @php
        use App\Enums\StatusPrereleaseSoTransaction;
    @endphp
    <div class="menu">
        @php
            $user = auth()->user();
            $status = $data->status;

            $canRndDrawingApprove =
                $user->hasPermissionTo('rnd_drawing_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value;

            $canRndBomApprove =
                $user->hasPermissionTo('rnd_bom_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value;

            $canAccountingApprove =
                $user->hasPermissionTo('accounting_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;

            $canAccountingRequestConfirmMargin =
                $user->hasPermissionTo('accounting_request_confirm_margin_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;
                
            $canMKTManagerConfirmMargin =
                $user->hasPermissionTo('mkt_manager_confirm_margin_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_MKT_MGR_CONFIRM_MARGIN->value;

            $canMKTStaffRelease =
                $user->hasPermissionTo('mkt_staff_release_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_RELEASE->value;

            $released = $user->hasPermissionTo('mkt_admin_reject_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::RELEASED->value;

            $canReject =
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canRndDrawingApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canRndBomApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canAccountingApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canAccountingRequestConfirmMargin) ||
                ($user->hasPermissionTo('mkt_admin_reject_prerelease_so_transaction') && $canMKTStaffRelease) ||
                $released;

        @endphp

        @if (auth()->user()->hasAnyPermission(['view_prerelease_so_transaction']))
            <a href="{{ route('prereleaseSoTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if ($canRndDrawingApprove || $canRndBomApprove || $canAccountingApprove || $canAccountingRequestConfirmMargin || $canMKTManagerConfirmMargin || $canMKTStaffRelease || $canReject)
            <a href="{{ route('prereleaseSoTransactionApprovalForm', $data->id) }}" class="item">
                @if ($canMKTStaffRelease)
                    Release
                @elseif ($canMKTManagerConfirmMargin)
                    Confirm Margin
                @elseif ($released)
                    Reject
                @else
                    Approval
                @endif
            </a>
        @endif

        @if (auth()->user()->hasPermissionTo('revise_prerelease_so_transaction') &&
            $data->status === StatusPrereleaseSoTransaction::REVISE_NEEDED->value
        )
            <a href="{{ route('prereleaseSoTransactionReviseForm', $data->id) }}" class="item">Revise</a>
        @endif
    </div>
</div>