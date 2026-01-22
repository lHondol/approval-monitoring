<?php

namespace App\Enums;

enum StatusDrawingTransaction: string
{
    case WAITING_1ST_APPROVAL = 'Waiting for 1st Approval';
    case WAITING_2ND_APPROVAL = 'Waiting for 2nd Approval';
    case DISTRIBUTED_COSTING_DONE = 'Distributed, Costing Done';
    case REVISE_NEEDED = 'Revise Needed';
    case DISTRIBUTED_WAITING_BOM_APPROVAL = 'Distributed, Waiting for BOM Approval';
    case DISTRIBUTED_WAITING_COSTING_APPROVAL = 'Distributed, Waiting for Costing Approval';
    case DISTRIBUTED_BOM_REJECTED = 'Distributed, BOM Rejected';
    case DISTRIBUTED_COSTING_REJECTED = 'Distributed, Costing Rejected';
}
