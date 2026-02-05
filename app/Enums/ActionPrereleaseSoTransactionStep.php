<?php

namespace App\Enums;

enum ActionPrereleaseSoTransactionStep: string
{
    case UPLOAD = 'Upload';
    case APPROVE_SALES_AREA = 'Approve - Sales Area';
    case APPROVE_RND_DRAWING = 'Approve - RnD Drawing';
    case APPROVE_RND_BOM = 'Approve - RnD BOM';
    case APPROVE_ACCOUNTING = 'Approve - Accounting';
    case APPROVE_IT = 'Approve - IT';
    case FINALIZE_MKT_STAFF = 'Finalize - MKT Staff';
    case REJECT = 'Reject';
    case UPLOAD_REVISED = 'Upload (Revised)';
}
