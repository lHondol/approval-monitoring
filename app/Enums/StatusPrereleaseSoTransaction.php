<?php

namespace App\Enums;

enum StatusPrereleaseSoTransaction: string
{
    case WAITING_SALES_AREA_APPROVAL = 'Waiting for Sales Area Approval';
    case WAITING_RND_DRAWING_APPROVAL = 'Waiting for RnD Drawing Approval';
    case WAITING_RND_BOM_APPROVAL = 'Waiting for RnD BOM Approval';
    case WAITING_ACCOUNTING_APPROVAL = 'Waiting for Accounting Approval';
    case WAITING_IT_APPROVAL = 'Waiting for IT Approval';
    case WAITING_MKT_MGR_CONFIRM_MARGIN = 'Waiting for MKT Manager Confirm Margin';
    case WAITING_MKT_STAFF_RELEASE = 'Waiting for MKT Staff Release';
    case RELEASED_WAITING_PO_KACA_APPROVAL = 'Released, Waiting for PO Kaca Approval';
    case RELEASED_PO_KACA_DONE = 'Released, PO Kaca Done';
    case REVISE_NEEDED = 'Revise Needed';
}
