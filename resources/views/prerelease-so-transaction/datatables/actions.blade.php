<div class="ui dropdown action-dropdown icon button !px-5 whitespace-nowrap">
    Actions <i class="dropdown icon"></i>
    @php
        use App\Enums\StatusPrereleaseSoTransaction;
    @endphp
    <div class="menu">
        @php
            $user = auth()->user();
            $status = $data->status;

            $canSalesAreaApprove =
                $user->hasPermissionTo('sales_area_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_SALES_AREA_APPROVAL->value;

            $canRndDrawingApprove =
                $user->hasPermissionTo('rnd_drawing_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_RND_DRAWING_APPROVAL->value;

            $canRndBomApprove =
                $user->hasPermissionTo('rnd_bom_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_RND_BOM_APPROVAL->value;

            $canAccountingApprove =
                $user->hasPermissionTo('accounting_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_ACCOUNTING_APPROVAL->value;

            $canItApprove =
                $user->hasPermissionTo('it_approve_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_IT_APPROVAL->value;

            $canMKTStaffRelease =
                $user->hasPermissionTo('mkt_staff_release_prerelease_so_transaction') &&
                $status === StatusPrereleaseSoTransaction::WAITING_MKT_STAFF_FINALIZE->value;


            $canReject =
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canSalesAreaApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canRndDrawingApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canRndBomApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canAccountingApprove) ||
                ($user->hasPermissionTo('reject_prerelease_so_transaction') && $canItApprove);
        @endphp

        @if (auth()->user()->hasAnyPermission(['view_prerelease_so_transaction']))
            <a href="{{ route('prereleaseSoTransactionDetail', $data->id) }}" class="item">Detail</a>
        @endif

        @if ($canSalesAreaApprove || $canRndDrawingApprove || $canRndBomApprove || $canAccountingApprove || $canItApprove || $canMKTStaffRelease || $canReject)
            <a href="{{ route('prereleaseSoTransactionApprovalForm', $data->id) }}" class="item">
                @if ($canMKTStaffRelease)
                    Finalize
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