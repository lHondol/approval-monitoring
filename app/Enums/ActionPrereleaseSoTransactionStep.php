<?php

namespace App\Enums;

enum ActionPrereleaseSoTransactionStep: string
{
    case UPLOAD = 'Upload';
    case APPROVE_MKT_STAFF = 'Approve - MKT Staff';
    case APPROVE_SALES_AREA = 'Approve - Sales Area';
    case APPROVE_RND_DRAWING = 'Approve - RnD Drawing';
    case APPROVE_RND_BOM = 'Approve - RnD BOM';
    case APPROVE_ACCOUNTING = 'Approve - Accounting';
    case REJECT = 'Reject';
    case UPLOAD_REVISED = 'Upload (Revised)';
}
