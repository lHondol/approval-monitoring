<?php

namespace App\Enums;

enum StatusPrereleaseSoTransaction: string
{
    case WAITING_SALES_AREA_APPROVAL = 'Waiting for Sales Area Approval';
    case WAITING_RND_DRAWING_APPROVAL = 'Waiting for RnD Drawing Approval';
    case WAITING_RND_BOM_APPROVAL = 'Waiting for RnD BOM Approval';
    case WAITING_ACCOUNTING_APPROVAL = 'Waiting for Accounting Approval';
    case WAITING_IT_APPROVAL = 'Waiting for IT Approval';
    case WAITING_MKT_STAFF_RELEASE = 'Waiting for MKT Staff Release';
    case RELEASED = 'Released';
    case REVISE_NEEDED = 'Revise Needed';
}
