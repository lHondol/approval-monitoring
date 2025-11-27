<?php

namespace App\Enums;

enum StatusEnum: string
{
    case WAITING_1ST_APPROVAL = 'Waiting for 1st Approval';
    case WAITING_2ND_APPROVAL = 'Waiting for 2nd Approval';
    case DISTRIBUTED = 'Distributed';
    case REVISE_NEEDED = 'Revise Needed';
}
