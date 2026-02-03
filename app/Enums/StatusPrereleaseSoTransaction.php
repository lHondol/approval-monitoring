<?php

namespace App\Enums;

enum StatusPrereleaseSoTransaction: string
{
    case WAITING_MKT_STAFF_APPROVAL = 'Waiting for MKT Staff Approval';
    case WAITING_SALES_AREA_APPROVAL = 'Waiting for Sales Area Approval';
    case WAITING_RND_DRAWING_APPROVAL = 'Waiting for RnD Drawing Approval';
    case WAITING_RND_BOM_APPROVAL = 'Waiting for RnD BOM Approval';
    case WAITING_ACCOUNTING_APPROVAL = 'Waiting for Accounting Approval';
    case FINALIZED = 'MKT Staff Finalized';
    case REVISE_NEEDED = 'Revise Needed';
}
