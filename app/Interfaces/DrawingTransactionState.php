<?php

namespace App\Interfaces;

interface DrawingTransactionState
{
    public function next(object $data = null);
    public function reject(object $data = null);
}
