<?php

namespace App\Enums;

enum ActionDrawingTransactionStep: string
{
    case UPLOAD = 'Upload';
    case APPROVE1 = 'Approve - 1';
    case APPROVE2 = 'Approve - 2';
    case APPROVE_BOM = 'Approve - BOM';
    case APPROVE_COSTING = 'Approve - Costing';
    case REJECT = 'Reject';
    case UPLOAD_REVISED = 'Upload (Revised)';
}
