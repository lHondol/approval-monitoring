<?php

namespace App\Enums;

enum ActionPrereleaseSoTransactionStep: string
{
    case UPLOAD = 'Upload';
    case APPROVE_SALES_AREA = 'Approve - Sales Area';
    case APPROVE_RND_DRAWING = 'Approve - RnD Drawing';
    case APPROVE_RND_BOM = 'Approve - RnD BOM';
    case APPROVE_ACCOUNTING = 'Approve - Accounting';
    case REQUEST_CONFIRM_MARGIN_ACCOUNTING = 'Request Confirm Margin - Accounting';
    case APPROVE_IT = 'Approve - IT';
    case CONFIRM_MARGIN_MKT_MGR = 'Confirm Margin - MKT Mgr';
    case RELEASED_MKT_STAFF = 'Released - MKT Staff';
    case APPROVE_PO_KACA = 'Approve - PO Kaca';
    case REJECT = 'Reject';
    case UPLOAD_REVISED = 'Upload (Revised)';
}
