<?php

namespace App\Interfaces;

interface PrereleaseSoTransactionState
{
    public function next(object $data = null);
    public function reject(object $data = null);
}
