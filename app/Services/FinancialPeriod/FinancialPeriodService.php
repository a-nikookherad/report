<?php

namespace App\Services\FinancialPeriod;

use App\Models\Bourse\FinancialPeriod;
use Hekmatinasser\Verta\Facades\Verta;

class FinancialPeriodService implements AddPeriodsInterface, GetPeriodsInterface
{

    /**
     * @param mixed $date
     * @param int $yearsAgo
     * @return void
     */
    public function addPeriods(mixed $date, int $yearsAgo)
    {
        for ($i = 0; $i <= $yearsAgo; $i++) {
            if (Verta::parse($date)->format("m-d") == "12-29") {
                $startSolar = Verta::parse(\verta(now()))->subYears($i)->startYear()->format("Y-m-d");
                $endSolar = Verta::parse(\verta(now()))->subYears($i)->endYear()->format("Y-m-d");
            } else {
                $endSolar = Verta::parse($date)->addYears($i)->format("Y-m-d");
                $startSolar = Verta::parse($endSolar)->subDays(364)->format("Y-m-d");
            }
            $date = [
                "solar_start_date" => $startSolar,
                "solar_end_date" => $endSolar,
                "start_date" => Verta::parse($startSolar)->datetime()->format("Y-m-d"),
                "end_date" => Verta::parse($endSolar)->datetime()->format("Y-m-d"),
                "close" => $i != 0,
            ];
            FinancialPeriod::query()
                ->updateOrCreate(["solar_end_date" => $date["solar_end_date"]], $date);
        }
    }

    /**
     * @param mixed $date
     * @return mixed|null
     */
    public function getPeriods(mixed $date): null|FinancialPeriod
    {
        $date = Verta::parse($date)->datetime()->format("Y-m-d");
        return FinancialPeriod::query()
            ->where("start_date", ">=", $date)
            ->where("end_date", "<=", $date)
            ->first();
    }
}
