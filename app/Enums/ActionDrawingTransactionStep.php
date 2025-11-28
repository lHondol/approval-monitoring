<?php

namespace App\Enums;

enum ActionDrawingTransactionStep: string
{
    case UPLOAD = 'Upload';
    case APPROVE1 = 'Approve - 1';
    case APPROVE2 = 'Approve - 2';
    case REJECT = 'Reject';
}
