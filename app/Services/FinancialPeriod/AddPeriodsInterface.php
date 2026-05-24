<?php

namespace App\Services\FinancialPeriod;

interface AddPeriodsInterface
{
    public function addPeriods($date,int $yearsAgo);
}
